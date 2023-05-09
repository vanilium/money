<?php
declare(strict_types=1);

namespace Tests\Resources;

class FakeCurrency extends \Vanilium\Money\Currency
{
    public static function name(): string
    {
        return self::class;
    }

    public static function sign(): string
    {
        return 'sign';
    }

    public static function iso(): string
    {
        return strtoupper(self::class);
    }
}