<?php
declare(strict_types=1);

namespace Vanilium\Money;

use Vanilium\Money\Interfaces\CurrencyRepositoryInterface;

/**
 * @method static void store(string $currency) Register new currency class
 * @method static Currency get(string $currency) Return currency object
 */
abstract class Currency
{
    private static CurrencyRepositoryInterface $repository;

    private static function init(): void
    {
        self::$repository ??= CurrencyRepository::instance();
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

    public static function __callStatic(string $name, array $arguments)
    {
        self::init();

        return match($name) {
            'store' => self::$repository->store(... $arguments),
            'get' => self::$repository->get(... $arguments),
            default => throw new \BadMethodCallException('call not exists method')
        };
    }
}