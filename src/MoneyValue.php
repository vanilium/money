<?php
declare(strict_types=1);

namespace Vanilium\Money;

class MoneyValue
{
    private const VALUE_MULTIPLEX = 100;

    private int $castedValue;
    public function __construct(float|int $value)
    {
        if(is_float($value)) {
            $value = $this->normalize($value);
        }

        $this->castedValue = (int) ($value * self::VALUE_MULTIPLEX);
    }

    private function normalize(float $value): float
    {
        $remainder = ((int) $value * self::VALUE_MULTIPLEX) - $value * self::VALUE_MULTIPLEX;

        if($remainder > 0) {
            throw new \Exception('Wrong format');
        }

        return $value;
    }

    /**
     * @param int $castValue
     * @return MoneyValue
     */
    private static function makeValueByCasted(int $castValue): MoneyValue
    {
        $newValue = new self(0);
        $newValue->castedValue = $castValue;

        return $newValue;
    }

    public static function multiplying(MoneyValue $value, int $multiplicator): MoneyValue
    {
        $calcValue = $value->castedValue * $multiplicator;

        return self::makeValueByCasted($calcValue);
    }

    /**
     * @param MoneyValue ...$values
     * @return self
     */
    public static function summarizing(MoneyValue ... $values): self
    {
        $calcValue = 0;
        foreach ($values as $value) {
            $calcValue += $value->castedValue;
        }

        return self::makeValueByCasted($calcValue);
    }

    /**
     * @param MoneyValue ...$values
     * @return self
     */
    public static function subtractizing(MoneyValue ... $values): self
    {
        $calcValue = array_shift($values)->castedValue;

        foreach ($values as $value) {
            $calcValue -= $value->castedValue;
        }

        if($calcValue < 0) {
            $calcValue = 0;
        }

        return self::makeValueByCasted($calcValue);
    }

    /**
     * @param int ...$parts
     * @return MoneyValue[]
     */
    public function split(int ...$parts): array
    {
        if(count($parts) === 1) {
            return [self::makeValueByCasted($this->castedValue)];
        }

        $commonOfParts = array_sum($parts);
        $valuesInParts = [];
        $remainder = [];
        foreach ($parts as $part) {
            $multiplyed = $this->castedValue * $part;
            $valuesInParts[] = (int) ($multiplyed / $commonOfParts);
            $remainder[] = $multiplyed % $commonOfParts;
        }

        $toIncrease = array_sum($remainder) / $commonOfParts;
        while($toIncrease) {
            $toIncrease--;
            $indexToIncrease = array_search(max($remainder), $remainder);
            $remainder[$indexToIncrease] = 0;
            $valuesInParts[$indexToIncrease] += 1;
        }


        return array_map(
            fn(int $castedValue) => self::makeValueByCasted($castedValue),
            $valuesInParts
        );
    }

    private function toValue(): float
    {
        return $this->castedValue / self::VALUE_MULTIPLEX;
    }

    public function __toString(): string
    {
        return (string) $this->toValue();
    }
}