<?php

namespace App\Models;

use App\Enums\StockTransactionStatus;
use App\Enums\StockTransactionType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransaction extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'document_number',
        'type',
        'status',
        'supplier_id',
        'customer_id',
        'occurred_on',
        'notes',
        'posted_at',
        'posted_by',
        'created_by',
    ];

    protected $casts = [
        'type' => StockTransactionType::class,
        'status' => StockTransactionStatus::class,
        'occurred_on' => 'date',
        'posted_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function postedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(StockTransactionLine::class)->orderBy('line_no');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(StockLedgerEntry::class);
    }

    public function isDraft(): bool
    {
        return $this->status === StockTransactionStatus::Draft;
    }

    public function isPosted(): bool
    {
        return $this->status === StockTransactionStatus::Posted;
    }
}
