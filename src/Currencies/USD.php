<?php
declare(strict_types=1);

namespace Vanilium\Money\Currencies;

use Vanilium\Money\Currency;

class USD extends Currency
{
    public static function name(): string
    {
        return 'Dollar';
    }

    public static function sign(): string
    {
        return '$';
    }

    static public function iso(): string
    {
        return 'USD';
    }
}