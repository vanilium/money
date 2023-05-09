<?php
declare(strict_types=1);

namespace Vanilium\Money;

abstract class Currency
{
    private static $repository;

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

    abstract public static function name(): string;

    abstract public static function sign(): string;

    abstract public static function iso(): string;

    public function __toString(): string
    {
        return static::iso();
    }

//    static public function make(string $createdCurrency): Currency
//    {
//        if(!self::$currenciesList[$createdCurrency]) {
//            throw new \Exception('Currency not exists');
//        }
//
//        if(!is_object(self::$currenciesList[$createdCurrency])) {
//            //Replace as inited object
//            self::store(new self::$currenciesList[$createdCurrency]);
//        }
//
//        return self::$currenciesList[$createdCurrency];
//    }

//    public static function setExchangeRatios(string $currencyIso, array $ratios): void
//    {
//        self::$exchanger->setExchangeRatios(self::$repository->get($currencyIso), $ratios);
//    }

    //exchange ratio
}