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
        MoneyValue|float|int $value,
        Currency|string $currency
    )
    {
        self::init();

        $this->value = $value instanceof MoneyValue ? $value : new MoneyValue($value);
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
            Currency::store($currencyClass);
        }

        $inited ??= true;
    }

    static public function parse(string $value): Money
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

        return new Money((float) $value, $currency);
    }

    public function __toString(): string
    {
        return $this->value .' '. $this->currency;
    }

    public function add(string|Money $toAdd): Money
    {
        if(is_string($toAdd)) {
            $toAdd = self::parse($toAdd);
        }

        if($this->currency !== $toAdd->currency) {
            //TODO: exchange to self currency, if exchange ratio exist
        }

        return new self(
            value: MoneyValue::summarizing($this->value, $toAdd->value),
            currency: $this->currency
        );
    }

    public function sub(string|Money $toSub): Money
    {
        if(is_string($toSub)) {
            $toSub = self::parse($toSub);
        }

        if($this->currency !== $toSub->currency) {
            //TODO: exchange to self currency, if exchange ratio exist
        }

        return new self(
            value: MoneyValue::subtractizing($this->value, $toSub->value),
            currency: $this->currency
        );
    }

    public function multiply(int $multiplicator): Money
    {
        return new self(
            value: MoneyValue::multiplying($this->value, $multiplicator),
            currency: $this->currency
        );
    }

    public function calcPercent(int $int): Money
    {
        //TODO: incomplite
        $calcValue = MoneyValue::multiplying($this->value, $int);

        return new self(
            value: MoneyValue::dividing($calcValue, 100),
            currency: $this->currency
        );
    }
}