<?php

namespace App\Services;

use App\Models\BusinessSequence;
use Illuminate\Support\Facades\DB;

class BusinessSequenceService
{
    public const KEY_STOCK_DOCUMENT = 'stock_transaction';

    public function nextFormattedNumber(int $businessId, string $sequenceKey, ?int $year = null): string
    {
        $year = $year ?? (int) now()->year;
        $n = $this->nextRawValue($businessId, $sequenceKey, $year);

        return sprintf('STK-%d-%06d', $year, $n);
    }

    public function nextRawValue(int $businessId, string $sequenceKey, ?int $year = null): int
    {
        return (int) DB::transaction(fn () => $this->incrementWithinTransaction($businessId, $sequenceKey, $year));
    }

    /**
     * Must be called inside an existing transaction for correct locking with sibling writes.
     */
    public function incrementWithinTransaction(int $businessId, string $sequenceKey, ?int $year = null): int
    {
        $year = $year ?? (int) now()->year;

        $row = BusinessSequence::query()
            ->where('business_id', $businessId)
            ->where('sequence_key', $sequenceKey)
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if (! $row) {
            BusinessSequence::query()->create([
                'business_id' => $businessId,
                'sequence_key' => $sequenceKey,
                'year' => $year,
                'last_value' => 0,
            ]);

            $row = BusinessSequence::query()
                ->where('business_id', $businessId)
                ->where('sequence_key', $sequenceKey)
                ->where('year', $year)
                ->lockForUpdate()
                ->firstOrFail();
        }

        $row->increment('last_value');

        return (int) $row->fresh()->last_value;
    }
}
