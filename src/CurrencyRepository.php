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

    private function __construct()
    {
    }

    public static function instance(): self
    {
        static $instance;

        return $instance ??= new self();
    }

    /**
     * @throws \Exception
     */
    public function store(string $currencyClassName): void
    {
        Currency::isCurrency($currencyClassName);

        $this->aliasesList[$currencyClassName::iso()] = $this->aliasesList[$currencyClassName::sign()] = $currencyClassName;
    }

    public function get(string $currencyAlias): Currency
    {
        if(empty($this->initiatedCurrencies[$currencyAlias])) {
            $currencyClass = ($this->aliasesList[$currencyAlias] ?? null) ?? (in_array($currencyAlias, $this->aliasesList) ? $currencyAlias : false );

            if($currencyClass) {
                $this->initiatedCurrencies[$currencyAlias] = new $currencyClass();
            }
        }

        return $this->initiatedCurrencies[$currencyAlias] ?? throw new \Exception('Currency not defined');
    }
}