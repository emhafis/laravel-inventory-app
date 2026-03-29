<?php

namespace App\Services;

use App\Enums\StockTransactionStatus;
use App\Enums\StockTransactionType;
use App\Models\ProductStockBalance;
use App\Models\StockLedgerEntry;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockPostingService
{
    public function post(StockTransaction $transaction): void
    {
        if (! $transaction->isDraft()) {
            throw new RuntimeException('Hanya dokumen draft yang dapat diposting.');
        }

        DB::transaction(function () use ($transaction) {
            $locked = StockTransaction::withoutGlobalScopes()
                ->whereKey($transaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== StockTransactionStatus::Draft) {
                throw new RuntimeException('Dokumen sudah tidak dalam status draft.');
            }

            $lines = StockTransactionLine::withoutGlobalScopes()
                ->where('stock_transaction_id', $locked->id)
                ->orderBy('line_no')
                ->lockForUpdate()
                ->get();

            if ($lines->isEmpty()) {
                throw new RuntimeException('Dokumen harus memiliki minimal satu baris.');
            }

            $productIds = $lines->pluck('product_id')->unique()->sort()->values();
            foreach ($productIds as $productId) {
                $this->ensureBalanceRow($locked->business_id, (int) $productId);
            }

            foreach ($lines as $line) {
                $this->applyLine($locked, $line);
            }

            $locked->update([
                'status' => StockTransactionStatus::Posted,
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);
        });
    }

    private function ensureBalanceRow(int $businessId, int $productId): void
    {
        $balance = ProductStockBalance::withoutGlobalScopes()
            ->where('business_id', $businessId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if (! $balance) {
            ProductStockBalance::withoutGlobalScopes()->create([
                'business_id' => $businessId,
                'product_id' => $productId,
                'quantity' => 0,
            ]);
            ProductStockBalance::withoutGlobalScopes()
                ->where('business_id', $businessId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();
        }
    }

    private function applyLine(StockTransaction $transaction, StockTransactionLine $line): void
    {
        $balance = ProductStockBalance::withoutGlobalScopes()
            ->where('business_id', $transaction->business_id)
            ->where('product_id', $line->product_id)
            ->lockForUpdate()
            ->firstOrFail();

        $change = match ($transaction->type) {
            StockTransactionType::In => (string) $line->quantity,
            StockTransactionType::Out => bcmul((string) $line->quantity, '-1', 4),
            StockTransactionType::Adjustment => (string) $line->quantity,
        };

        $newQty = bcadd((string) $balance->quantity, $change, 4);
        if (bccomp($newQty, '0', 4) < 0) {
            throw new RuntimeException('Stok tidak mencukupi untuk produk pada baris #'.$line->line_no);
        }

        $balance->update(['quantity' => $newQty]);

        StockLedgerEntry::withoutGlobalScopes()->create([
            'business_id' => $transaction->business_id,
            'product_id' => $line->product_id,
            'stock_transaction_id' => $transaction->id,
            'stock_transaction_line_id' => $line->id,
            'change_qty' => $change,
            'quantity_after' => $newQty,
            'recorded_at' => now(),
        ]);
    }
}
