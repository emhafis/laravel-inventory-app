<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('stock_transaction_id')->constrained()->restrictOnDelete();
            $table->foreignId('stock_transaction_line_id')->constrained()->restrictOnDelete();
            $table->decimal('change_qty', 18, 4);
            $table->decimal('quantity_after', 18, 4);
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['business_id', 'product_id', 'recorded_at']);
            $table->index(['stock_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledger_entries');
    }
};
