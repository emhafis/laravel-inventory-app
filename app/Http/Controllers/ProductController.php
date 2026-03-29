<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStockBalance;
use App\Models\Unit;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'unit', 'stockBalance'])
            ->orderBy('name')
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $units = Unit::query()->where('is_active', true)->orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $product = Product::query()->create([
                'business_id' => BusinessContext::id(),
                'category_id' => $request->integer('category_id'),
                'unit_id' => $request->integer('unit_id'),
                'sku' => $request->string('sku'),
                'barcode' => $request->input('barcode'),
                'name' => $request->string('name'),
                'description' => $request->input('description'),
                'cost_price' => $request->input('cost_price'),
                'sell_price' => $request->input('sell_price'),
                'min_stock_level' => $request->input('min_stock_level'),
                'is_active' => $request->boolean('is_active'),
            ]);

            ProductStockBalance::query()->create([
                'business_id' => BusinessContext::id(),
                'product_id' => $product->id,
                'quantity' => 0,
            ]);
        });

        return redirect()->route('products.index')->with('status', 'Produk berhasil dibuat.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'stockBalance']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $units = Unit::query()->where('is_active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'category_id' => $request->integer('category_id'),
            'unit_id' => $request->integer('unit_id'),
            'sku' => $request->string('sku'),
            'barcode' => $request->input('barcode'),
            'name' => $request->string('name'),
            'description' => $request->input('description'),
            'cost_price' => $request->input('cost_price'),
            'sell_price' => $request->input('sell_price'),
            'min_stock_level' => $request->input('min_stock_level'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('products.show', $product)->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $qty = (string) ($product->stockBalance?->quantity ?? '0');
        if (
            $product->stockTransactionLines()->exists()
            || bccomp($qty, '0', 4) !== 0
        ) {
            return back()->with('error', 'Produk tidak dapat dihapus karena memiliki riwayat stok atau saldo tidak nol.');
        }

        DB::transaction(function () use ($product) {
            $product->stockBalance?->delete();
            $product->delete();
        });

        return redirect()->route('products.index')->with('status', 'Produk dihapus.');
    }
}
