<?php
declare(strict_types=1);

namespace Vanilium\Money;

use Vanilium\Money\Currencies\EUR;
use Vanilium\Money\Currencies\USD;

class Money
{
    public readonly MoneyValue $value;
    public readonly Currency $currency;
    public function __construct(
        mixed $value,
        string $currency
    )
    {
        self::init();

        $this->value = new MoneyValue($value);
        $this->currency = new USD();
    }

    static public function init(): void
    {
        static $inited;

        if($inited) return;

        $currencies = [
            USD::class,
            EUR::class,
        ];

        foreach ($currencies as $currencyClass) {
            CurrencyRepository::store($currencyClass);
        }

        $inited ??= true;
    }

    static public function make(string $value): Money
    {
        //remove any number ranks
        $value = str_replace([' ', ','], '', $value);

        /* Valid values: 123USD, 123.45USD, .45USD, 123$, 123.45$, .45$, USD123, USD123.45, USD.45, $123, $123.45, $.45*/
        if(!preg_match('/^(?:(\d+(?:\.\d{1,2})?|\.\d{1,2})(?>([^.\d]+))|([^.\d]+?)(?>(\d+(?:\.\d{1,2})?|\.\d{1,2})))$/', $value, $matches)) {
            throw new \Exception('Not valid value');
        }

        if(count($matches) > 3) {
            [, , ,$value, $currency] = $matches;
        } else {
            [, $value, $currency] = $matches;
        }

        if(is_numeric($currency)) {
            [$value, $currency] = [$currency, $value];
        }

        return new Money($value, $currency);
    }

    public function __toString(): string
    {
        return $this->value .' '. $this->currency;
    }

    public function exchangeRatioTo(string $relatedToClass)
    {
        //TODO
    }
}