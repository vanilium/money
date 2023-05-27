<?php
declare(strict_types=1);

namespace Tests;

use Exception;
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
        $money = Money::parse($parseValue);

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

        Money::parse($parseValue);
    }

    public function test_add()
    {
        $money = Money::parse('10 USD');

        $this->assertEquals(10, (string) $money->value);

        $moneyPlus = $money->add('5 USD');

        $this->assertInstanceOf(Money::class, $moneyPlus);
        $this->assertEquals(15, (string) $moneyPlus->value);
        $this->assertEquals('USD', $moneyPlus->currency);
    }

    public function test_sub()
    {
        $money = Money::parse('10 USD');

        $this->assertEquals(10, (string) $money->value);

        $moneySub = $money->sub('5 USD');

        $this->assertInstanceOf(Money::class, $moneySub);
        $this->assertEquals(5, (string) $moneySub->value);
        $this->assertEquals('USD', $moneySub->currency);
    }

    public function test_multiply()
    {
        $money = Money::parse('10 USD');

        $this->assertEquals(10, (string) $money->value);

        $fiftyPencent = $money->multiply(2);

        $this->assertEquals(20, (string) $fiftyPencent->value);
    }

    public function test_get_percent()
    {
        $this->markTestIncomplete();
        $money = Money::parse('10 USD');

        $this->assertEquals(10, (string) $money->value);

        $fiftyPencent = $money->calcPercent(50);

        $this->assertEquals(5, (string) $fiftyPencent->value);
    }

    public function test_exchange_ratio_to()
    {
        $this->markTestIncomplete();
        Money::init();

        Currency::setExchangeRatios(USD::class, [
            'USD' => 1,
            'EUR' => .5
        ]);

        $moneyUsd = Money::parse('5USD');

        $this->assertEquals(1, $moneyUsd->exchangeRatioTo(USD::class));
        $this->assertEquals(.5, $moneyUsd->exchangeRatioTo(EUR::class));

        $moneyEur = Money::parse('5EUR');

        $this->assertEquals(1, $moneyEur->exchangeRatioTo(EUR::class));
        $this->assertEquals(2, $moneyEur->exchangeRatioTo(USD::class));
    }



//    public function test_money_value()
//    {
//        $value1 = new MoneyValue(1);
//        $value2 = new MoneyValue(2);
//
//        $valueSum = MoneyValue::summarize($value1, $value2);
//        $true = true;
//    }
}