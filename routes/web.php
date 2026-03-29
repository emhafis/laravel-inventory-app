<?php

use App\Http\Controllers\BusinessSetupController;
use App\Http\Controllers\BusinessSwitchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/business/select', [BusinessSwitchController::class, 'select'])->name('business.select');
    Route::post('/business/switch', [BusinessSwitchController::class, 'switch'])->name('business.switch');

    Route::get('/businesses/create', [BusinessSetupController::class, 'create'])->name('businesses.create');
    Route::post('/businesses', [BusinessSetupController::class, 'store'])->name('businesses.store');

    Route::middleware('business')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('units', UnitController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class);

        Route::resource('stock-transactions', StockTransactionController::class);
        Route::post('stock-transactions/{stock_transaction}/post', [StockTransactionController::class, 'submitPost'])
            ->name('stock-transactions.post');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/stock-on-hand', [ReportController::class, 'stockOnHand'])->name('reports.stock-on-hand');
        Route::get('reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
        Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
