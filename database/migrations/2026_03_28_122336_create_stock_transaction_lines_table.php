<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transaction_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->unsignedInteger('line_no')->default(1);
            $table->timestamps();

            $table->index(['business_id', 'product_id']);
            $table->index(['stock_transaction_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_lines');
    }
};
