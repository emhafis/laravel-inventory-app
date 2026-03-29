<?php

namespace App\Http\Controllers;

use App\Enums\StockTransactionStatus;
use App\Models\Product;
use App\Models\ProductStockBalance;
use App\Models\StockTransaction;
use App\Support\BusinessContext;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $bid = BusinessContext::id();

        $productCount = Product::query()->where('is_active', true)->count();

        $lowStockCount = ProductStockBalance::query()
            ->join('products', 'products.id', '=', 'product_stock_balances.product_id')
            ->where('products.is_active', true)
            ->whereColumn('product_stock_balances.quantity', '<', 'products.min_stock_level')
            ->count();

        $draftStockCount = StockTransaction::query()
            ->where('status', StockTransactionStatus::Draft)
            ->count();

        $recentMovements = StockTransaction::query()
            ->with(['lines.product'])
            ->where('status', StockTransactionStatus::Posted)
            ->latest('posted_at')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'productCount',
            'lowStockCount',
            'draftStockCount',
            'recentMovements'
        ));
    }
}
