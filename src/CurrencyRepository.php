<?php
declare(strict_types=1);

namespace Vanilium\Money;

use Vanilium\Money\Interfaces\CurrencyRepositoryInterface;

/**
 * @method static store(string $currencyClassName): void
 * @method static get(string $currencyAlias): Currency string $currencyAlias is currency iso or currency sign
 */
final class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @var array string[]
     */
    private array $aliasesList = [];

    /**
     * @var array Currency[]
     */
    private array $initiatedCurrencies = [];

    public static function instance(): self
    {
        static $instance;

        return $instance ??= new self();
    }

    public function store(string $currencyClassName): void
    {
        Currency::isCurrency($currencyClassName);

        $this->aliasesList[$currencyClassName::iso()] = $currencyClassName;
        $this->aliasesList[$currencyClassName::sign()] = $currencyClassName;
    }

    public function get(string $currencyAlias): ?Currency
    {
        if(empty($this->initiatedCurrencies[$currencyAlias])) {
            if($currencyClass = $this->aliasesList[$currencyAlias]) {
                $this->initiatedCurrencies[$currencyAlias] = new $currencyClass();
            }
        }

        return $this->initiatedCurrencies[$currencyAlias] ?? null;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if(method_exists(self::class, $name)) {
            return call_user_func([self::instance(), $name], $arguments);
        }
    }
}