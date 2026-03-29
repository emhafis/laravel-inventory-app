<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockBalance;
use App\Models\StockLedgerEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function stockOnHand(): View
    {
        $rows = ProductStockBalance::query()
            ->with(['product.unit', 'product.category'])
            ->join('products', 'products.id', '=', 'product_stock_balances.product_id')
            ->where('products.is_active', true)
            ->orderBy('products.name')
            ->select('product_stock_balances.*')
            ->paginate(30);

        $valuation = ProductStockBalance::query()
            ->join('products', 'products.id', '=', 'product_stock_balances.product_id')
            ->where('products.is_active', true)
            ->selectRaw(
                'SUM(product_stock_balances.quantity * products.cost_price) as total_cost, '.
                'SUM(product_stock_balances.quantity * products.sell_price) as total_sell'
            )
            ->first();

        return view('reports.stock-on-hand', compact('rows', 'valuation'));
    }

    public function movements(Request $request): View
    {
        $query = StockLedgerEntry::query()
            ->with(['product.unit', 'stockTransaction']);

        if ($request->filled('from')) {
            $query->whereDate('recorded_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('recorded_at', '<=', $request->date('to'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }

        $entries = $query->latest('recorded_at')->paginate(40)->withQueryString();
        $products = Product::query()->orderBy('name')->get();

        return view('reports.movements', compact('entries', 'products'));
    }

    public function lowStock(): View
    {
        $rows = ProductStockBalance::query()
            ->with(['product.unit', 'product.category'])
            ->join('products', 'products.id', '=', 'product_stock_balances.product_id')
            ->where('products.is_active', true)
            ->whereColumn('product_stock_balances.quantity', '<', 'products.min_stock_level')
            ->orderBy('products.name')
            ->select('product_stock_balances.*')
            ->paginate(30);

        return view('reports.low-stock', compact('rows'));
    }

    public function index(): View
    {
        return view('reports.index');
    }
}
