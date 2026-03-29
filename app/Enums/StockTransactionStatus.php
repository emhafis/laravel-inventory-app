<?php

namespace App\Enums;

enum StockTransactionStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Voided = 'voided';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Posted => 'Diposting',
            self::Voided => 'Dibatalkan',
        };
    }
}
