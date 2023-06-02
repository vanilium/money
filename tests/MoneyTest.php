<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Vanilium\Money\Currencies\EUR;
use Vanilium\Money\Currencies\USD;
use Vanilium\Money\Money;

class MoneyTest extends TestCase
{
    public static function provideCorrectMoneyListCases()
    {
        return [
            '5+5 USD' => [
                [
                    new Money(5, USD::class),
                    new Money(5, USD::class),
                ],
                10
            ],
            '5+5+5 USD' => [
                [
                    new Money(5, USD::class),
                    new Money(5, USD::class),
                    new Money(5, USD::class),
                ],
                15
            ],
            '5+10 USD' => [
                [
                    new Money(5, USD::class),
                    new Money(10, USD::class),
                ],
                15
            ],
            '5+.5 USD' => [
                [
                    new Money(5, USD::class),
                    new Money(.5, USD::class),
                ],
                5.5
            ],
        ];
    }

    public static function provideIncorrectMoneyListCases()
    {
        return [
            'One argument' => [
                [
                    new Money(5, USD::class)
                ],
                'Expecting 2 or more money arguments'
            ],
            'Different currencies argument' => [
                [
                    new Money(5, USD::class),
                    new Money(5, EUR::class),
                ],
                'It is impossible to sum money in different currencies'
            ],
        ];
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

    public static function provideParseIncorrectCases(): array
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

    public static function provideSplitCases(): array
    {
        return [
            'Split to 2 group by one param equally' => [
                10,
                [2],
                2,
                [5, 5]
            ],
            'Split to 3 group by one param not equally' => [
                10,
                [3],
                3,
                [3.33, 3.33, 3.34]
            ],
            'Split to 3 different group by one param' => [
                10,
                [4,2,1],
                3,
                [5.71, 2.86, 1.43]
            ],
        ];
    }

    public function test_instance()
    {
        $moneyValue = 5;
        $moneyCurrency = "USD"; //$
        $money = new Money($moneyValue, $moneyCurrency);

        $this->assertEquals($moneyValue, $money->value);
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

        $this->assertEquals($expectedValue, $money->value);
        $this->assertEquals($expectedCurrency, $money->currency);
    }

    /**
     * @dataProvider provideParseIncorrectCases
     */
    public function test_parse_incorrect(string $parseValue)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not valid value');

        Money::parse($parseValue);
    }

    /**
     * @dataProvider provideCorrectMoneyListCases
     */
    public function test_sum(array $moneyList, int|float $expectMoneyValueResult)
    {
        $moneySum = Money::sum(... $moneyList);

        $this->assertEquals($expectMoneyValueResult, $moneySum->value);
    }

    /**
     * @dataProvider provideIncorrectMoneyListCases
     */
    public function test_sum_incorrect_cases(array $moneyList, string $expectExceptionMessage)
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectExceptionMessage);

        Money::sum(... $moneyList);
    }

    /**
     * @dataProvider provideSplitCases
     */
    public function test_split(int $initMoneyValue, array $splitOn, int $expectCount, array $expectValues)
    {
        $money = new Money($initMoneyValue, USD::class );

        $moneyStack = Money::split($money, ... $splitOn);

        $this->assertCount($expectCount, $moneyStack);
        $this->assertContainsOnlyInstancesOf(Money::class, $moneyStack);
        $this->assertEquals(
            $initMoneyValue,
            Money::sum(...$moneyStack)->value
        );

        $this->assertEqualsCanonicalizing($expectValues, array_map(fn(Money $money) => $money->value, $moneyStack));
    }

    public function test_get_percent()
    {
        $this->markTestIncomplete();
        $money = Money::parse('10 USD');

        $this->assertEquals(10, $money->value);

        $fiftyPencent = $money->calcPercent(50);

        $this->assertEquals(5, $fiftyPencent->value);
    }
}