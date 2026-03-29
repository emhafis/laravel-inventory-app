<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLedgerEntry extends Model
{
    use BelongsToBusiness;

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'product_id',
        'stock_transaction_id',
        'stock_transaction_line_id',
        'change_qty',
        'quantity_after',
        'recorded_at',
    ];

    protected $casts = [
        'change_qty' => 'decimal:4',
        'quantity_after' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockTransaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class);
    }

    public function stockTransactionLine(): BelongsTo
    {
        return $this->belongsTo(StockTransactionLine::class);
    }
}
