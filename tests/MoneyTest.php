<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Vanilium\Money\Currencies\USD;
use Vanilium\Money\Currency;
use Vanilium\Money\Money;

class MoneyTest extends TestCase
{
    public function test_class_exist()
    {
        $this->assertTrue(class_exists(Money::class));
    }

    /**
     * @return array[]
     */
    public static function provideParsePositiveCases(): array
    {
        return [
            '123.45USD' => [
                '123.45USD',
                '123.45',
                'USD'
            ],
            '123USD' => [
                '123USD',
                '123',
                'USD'
            ],
            '.45USD' => [
                '.45USD',
                '0.45',
                'USD'
            ],
            'USD123.45' => [
                'USD123.45',
                '123.45',
                'USD'
            ],
            'USD123' => [
                'USD123',
                '123',
                'USD'
            ],
            'USD.45' => [
                'USD.45',
                '0.45',
                'USD'
            ],
            '123.45 USD' => [
                '123.45 USD',
                '123.45',
                'USD'
            ],
            '123 USD' => [
                '123 USD',
                '123',
                'USD'
            ],
            '.45 USD' => [
                '.45 USD',
                '0.45',
                'USD'
            ],
            'USD 123.45' => [
                'USD 123.45',
                '123.45',
                'USD'
            ],
            'USD 123' => [
                'USD 123',
                '123',
                'USD'
            ],
            'USD .45' => [
                'USD .45',
                '0.45',
                'USD'
            ],

            '123.45$' => [
                '123.45$',
                '123.45',
                'USD'
            ],
            '123$' => [
                '123$',
                '123',
                'USD'
            ],
            '.45$' => [
                '.45$',
                '0.45',
                'USD'
            ],
            '$123.45' => [
                '$123.45',
                '123.45',
                'USD'
            ],
            '$123' => [
                '$123',
                '123',
                'USD'
            ],
            '$.45' => [
                '$.45',
                '0.45',
                'USD'
            ],
            '123.45 $' => [
                '123.45 $',
                '123.45',
                'USD'
            ],
            '123 $' => [
                '123 $',
                '123',
                'USD'
            ],
            '.45 $' => [
                '.45 $',
                '0.45',
                'USD'
            ],
            '$ 123.45' => [
                '$ 123.45',
                '123.45',
                'USD'
            ],
            '$ 123' => [
                '$ 123',
                '123',
                'USD'
            ],
            '$ .45' => [
                '$ .45',
                '0.45',
                'USD'
            ],
        ];
    }

    public static function provideParseNegativeCases(): array
    {
        return [
            '123.4567USD' => [
                '123.4567USD'
            ],
            'USD123.4567' => [
                'USD123.4567'
            ],
            '123.4567 USD' => [
                '123.4567 USD'
            ],
            'USD 123.4567' => [
                'USD 123.4567'
            ],
            '123.4567$' => [
                '123.4567$'
            ],
            '$123.4567' => [
                '$123.4567'
            ],
            '123.4567 $' => [
                '123.4567 $'
            ],
            '$ 123.4567' => [
                '$ 123.4567'
            ],
            '$' => [
                '$'
            ],
            '123.45' => [
                '123.45'
            ],
        ];
    }

    public function test_instance()
    {
        $moneyValue = 5;
        $moneyCurrency = "USD"; //$
        $money = new Money($moneyValue, $moneyCurrency);

        $this->assertEquals((string) $moneyValue, $money->value);
        $this->assertEquals($moneyCurrency, $money->currency);
    }

    public function test_to_string()
    {
        $moneyValue = 5;
        $moneyCurrency = "USD";
        $money = new Money($moneyValue, $moneyCurrency);

        $this->assertEquals(sprintf('%1$s %2$s', $moneyValue, $moneyCurrency), (string) $money);
    }

    /**
     * @dataProvider provideParsePositiveCases
     */
    public function test_parse(string $parseValue, string $expectedValue, string $expectedCurrency)
    {
        $money = Money::make($parseValue);

        $this->assertEquals($expectedValue, (string) $money->value);
        $this->assertEquals($expectedCurrency, $money->currency);
    }

    /**
     * @dataProvider provideParseNegativeCases
     */
    public function test_parse_negative(string $parseValue)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not valid value');

        Money::make($parseValue);
    }

    public function test_exchange_ratio_to()
    {
        Money::init();

        Currency::setExchangeRatios(USD::class, [
            'USD' => 1,
            'EUR' => .5
        ]);

        $moneyUsd = Money::make('5USD');

        $this->assertEquals(1, $moneyUsd->exchangeRatioTo(USD::class));
        $this->assertEquals(.5, $moneyUsd->exchangeRatioTo(EUR::class));

        $moneyEur = Money::make('5EUR');

        $this->assertEquals(1, $moneyEur->exchangeRatioTo(EUR::class));
        $this->assertEquals(2, $moneyEur->exchangeRatioTo(USD::class));
    }
}