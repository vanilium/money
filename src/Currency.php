<?php
declare(strict_types=1);

namespace Vanilium\Money;


abstract class Currency
{
    public function __construct()
    {
        self::$repository = CurrencyRepository::instance();
    }

    /**
     * @param object|string $className
     * @return bool
     * @throws \Exception
     */
    final public static function isCurrency(object|string $className): bool
    {
        return is_subclass_of($className, self::class) ?: throw new \Exception('Class are not a currency');
    }

    abstract static public function name(): string;

    abstract static public function sign(): string;

    abstract static public function iso(): string;

    public function __toString(): string
    {
        return static::iso();
    }
}