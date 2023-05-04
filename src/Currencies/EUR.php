<?php
declare(strict_types=1);

namespace Vanilium\Money\Currencies;

use Vanilium\Money\Currency;

class EUR extends Currency
{

    static public function name(): string
    {
        return 'Euro';
    }

    static public function sign(): string
    {
        return '€';
    }

    static public function iso(): string
    {
        return 'EUR';
    }
}