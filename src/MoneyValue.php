<?php
declare(strict_types=1);

namespace Vanilium\Money;

class MoneyValue
{
    private const VALUE_MULTIPLEX = 100;

    private int $castedValue;
    public function __construct(string|float|int $value)
    {
        if(!is_int($value)) {
            $value = $this->normalize($value);
        }

        $this->castedValue = (int) ($value * self::VALUE_MULTIPLEX);
    }

    private function normalize(string|float $value): float
    {
        if(is_string($value)) {
            if(!is_numeric($value)) {
                throw new \Exception('Wrong format');
            }

            $value = (float) $value;
        }

        $remainder = ((int) $value * self::VALUE_MULTIPLEX) - $value * self::VALUE_MULTIPLEX;
        if($remainder > 0) {
            throw new \Exception('Wrong format');
        }

        return $value;
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