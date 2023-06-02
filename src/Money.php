<?php
declare(strict_types=1);

namespace Vanilium\Money;

use Vanilium\Money\Currencies\EUR;
use Vanilium\Money\Currencies\USD;

/**
 * @property string $value
 */
class Money
{
    private readonly MoneyValue $value;
    public readonly Currency $currency;

    /**
     * @param MoneyValue|float|int $value
     * @param Currency|string $currency
     * @throws \Exception
     */
    public function __construct(
        MoneyValue|float|int $value,
        Currency|string $currency
    )
    {
        self::init();

        $this->value = $value instanceof MoneyValue ? $value : new MoneyValue($value);
        $this->currency = is_string($currency) ? Currency::get($currency) : $currency;
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

    public function __get(string $name)
    {
        return match ($name) {
            'value' => (string) $this->value,
            default => null
        };
    }


    /**
     * @param Money ...$toSumMoneys
     * @return Money
     * @throws \Exception
     */
    public static function sum(Money ... $toSumMoneys): Money
    {
        if(count($toSumMoneys) < 2) {
            throw new \Exception('Expecting 2 or more money arguments');
        }

        $moneyCurrency = array_reduce(
            $toSumMoneys,
            fn(Currency $currency, Money $currentMoney) => is_a($currentMoney->currency, get_class($currency))
                ? $currentMoney->currency
                : throw new \Exception('It is impossible to sum money in different currencies'),
            current($toSumMoneys)->currency
        );

        return new Money(
            value: MoneyValue::summarizing(... array_map(fn(Money $money) => $money->value, $toSumMoneys)),
            currency: $moneyCurrency
        );
    }

    /**
     * @param Money $forSplit
     * @param int[] $parts Array with numbers, every number is the part's weight in the total amount. One number splits the full amount into an equal number of parts
     * @return Money[] Array of parts
     */
    public static function split(Money $forSplit, int ... $parts): array
    {
        if(count($parts) === 1) {
            $parts = array_fill(0, $parts[0], 1);
        }

        return array_map(
            fn(MoneyValue $moneyValue): Money => new Money($moneyValue, $forSplit->currency),
            $forSplit->value->split(... $parts)
        );
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

    public function calcPercent(int|float $int): Money
    {
        return new self(0, $this->currency);
        //TODO: incomplite
        $calcValue = MoneyValue::multiplying($this->value, $int);

        return new self(
            value: MoneyValue::dividing($calcValue, 100),
            currency: $this->currency
        );
    }
}