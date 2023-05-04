<?php
declare(strict_types=1);

namespace Vanilium\Money\Interfaces;

use Vanilium\Money\Currency;

interface CurrencyRepositoryInterface
{
    public function store(string $currencyClassName): void;

    public function get(string $currencyAlias): ?Currency;
}