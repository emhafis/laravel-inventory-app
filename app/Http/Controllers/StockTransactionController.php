<?php

namespace App\Http\Controllers;

use App\Enums\StockTransactionStatus;
use App\Enums\StockTransactionType;
use App\Http\Requests\StoreStockTransactionRequest;
use App\Http\Requests\UpdateStockTransactionRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use App\Models\Supplier;
use App\Services\BusinessSequenceService;
use App\Services\StockPostingService;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class StockTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockTransaction::query()->with(['supplier', 'customer'])->latest('occurred_on');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('stock-transactions.index', compact('transactions'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $typeParam = $request->string('type');
        if ($typeParam === '') {
            return redirect()->route('stock-transactions.create', ['type' => StockTransactionType::In->value]);
        }

        try {
            $type = StockTransactionType::from($typeParam);
        } catch (\ValueError) {
            $type = StockTransactionType::In;
        }

        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get();
        $customers = Customer::query()->where('is_active', true)->orderBy('name')->get();

        return view('stock-transactions.create', compact('type', 'products', 'suppliers', 'customers'));
    }

    public function store(
        StoreStockTransactionRequest $request,
        BusinessSequenceService $sequenceService
    ): RedirectResponse {
        $bid = BusinessContext::id();
        $type = StockTransactionType::from($request->string('type'));

        try {
            $doc = DB::transaction(function () use ($request, $sequenceService, $bid, $type) {
                $occurred = $request->date('occurred_on');
                $year = (int) $occurred->format('Y');

                $next = $sequenceService->incrementWithinTransaction(
                    (int) $bid,
                    BusinessSequenceService::KEY_STOCK_DOCUMENT,
                    $year
                );

                $documentNumber = sprintf('STK-%d-%06d', $year, $next);

                $doc = StockTransaction::query()->create([
                    'business_id' => $bid,
                    'document_number' => $documentNumber,
                    'type' => $type,
                    'status' => StockTransactionStatus::Draft,
                    'supplier_id' => $type === StockTransactionType::In ? $request->input('supplier_id') : null,
                    'customer_id' => $type === StockTransactionType::Out ? $request->input('customer_id') : null,
                    'occurred_on' => $occurred,
                    'notes' => $request->input('notes'),
                    'created_by' => auth()->id(),
                ]);

                foreach ($request->input('lines', []) as $i => $line) {
                    StockTransactionLine::query()->create([
                        'business_id' => $bid,
                        'stock_transaction_id' => $doc->id,
                        'product_id' => $line['product_id'],
                        'quantity' => $line['quantity'],
                        'unit_cost' => $type === StockTransactionType::In
                            ? ($line['unit_cost'] ?? null)
                            : null,
                        'line_no' => $i + 1,
                    ]);
                }

                return $doc;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal menyimpan dokumen: '.$e->getMessage());
        }

        return redirect()
            ->route('stock-transactions.show', $doc)
            ->with('status', 'Dokumen draft disimpan.');
    }

    public function show(StockTransaction $stockTransaction): View
    {
        $stockTransaction->load(['lines.product.unit', 'supplier', 'customer', 'createdByUser', 'postedByUser']);

        return view('stock-transactions.show', ['transaction' => $stockTransaction]);
    }

    public function edit(StockTransaction $stockTransaction): View|RedirectResponse
    {
        if (! $stockTransaction->isDraft()) {
            return redirect()->route('stock-transactions.show', $stockTransaction);
        }

        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get();
        $customers = Customer::query()->where('is_active', true)->orderBy('name')->get();

        $stockTransaction->load('lines');

        return view('stock-transactions.edit', [
            'transaction' => $stockTransaction,
            'products' => $products,
            'suppliers' => $suppliers,
            'customers' => $customers,
        ]);
    }

    public function update(
        UpdateStockTransactionRequest $request,
        StockTransaction $stockTransaction
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $stockTransaction) {
                $stockTransaction->update([
                    'supplier_id' => $stockTransaction->type === StockTransactionType::In
                        ? $request->input('supplier_id')
                        : null,
                    'customer_id' => $stockTransaction->type === StockTransactionType::Out
                        ? $request->input('customer_id')
                        : null,
                    'occurred_on' => $request->date('occurred_on'),
                    'notes' => $request->input('notes'),
                ]);

                $stockTransaction->lines()->delete();

                $bid = $stockTransaction->business_id;
                foreach ($request->input('lines', []) as $i => $line) {
                    StockTransactionLine::query()->create([
                        'business_id' => $bid,
                        'stock_transaction_id' => $stockTransaction->id,
                        'product_id' => $line['product_id'],
                        'quantity' => $line['quantity'],
                        'unit_cost' => $stockTransaction->type === StockTransactionType::In
                            ? ($line['unit_cost'] ?? null)
                            : null,
                        'line_no' => $i + 1,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui dokumen: '.$e->getMessage());
        }

        return redirect()
            ->route('stock-transactions.show', $stockTransaction)
            ->with('status', 'Dokumen diperbarui.');
    }

    public function destroy(StockTransaction $stockTransaction): RedirectResponse
    {
        if (! $stockTransaction->isDraft()) {
            return back()->with('error', 'Hanya dokumen draft yang dapat dihapus.');
        }

        DB::transaction(function () use ($stockTransaction) {
            $stockTransaction->lines()->delete();
            $stockTransaction->delete();
        });

        return redirect()->route('stock-transactions.index')->with('status', 'Dokumen dihapus.');
    }

    public function submitPost(
        StockTransaction $stockTransaction,
        StockPostingService $postingService
    ): RedirectResponse {
        try {
            $postingService->post($stockTransaction);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Posting gagal: '.$e->getMessage());
        }

        return redirect()
            ->route('stock-transactions.show', $stockTransaction)
            ->with('status', 'Dokumen berhasil diposting dan stok diperbarui.');
    }
}
