<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSequence extends Model
{
    protected $fillable = [
        'business_id',
        'sequence_key',
        'year',
        'last_value',
    ];

    protected $casts = [
        'year' => 'integer',
        'last_value' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
