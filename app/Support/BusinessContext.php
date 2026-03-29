<?php

namespace App\Support;

use App\Models\Business;

class BusinessContext
{
    private static ?int $id = null;

    private static ?Business $business = null;

    public static function set(?Business $business): void
    {
        self::$business = $business;
        self::$id = $business?->id;
    }

    public static function clear(): void
    {
        self::$business = null;
        self::$id = null;
    }

    public static function id(): ?int
    {
        return self::$id;
    }

    public static function business(): ?Business
    {
        return self::$business;
    }
}
