<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Resources\FakeCurrency;
use Vanilium\Money\Currency;
use Vanilium\Money\CurrencyRepository;
use Vanilium\Money\Interfaces\CurrencyRepositoryInterface;

class CurrencyTest extends TestCase
{
    public function test_is_currency_successful()
    {
        $this->assertTrue(Currency::isCurrency(FakeCurrency::class));
        $this->assertTrue(Currency::isCurrency(new FakeCurrency));
    }

    public function test_is_currency_failure()
    {
        $this->expectException(\Exception::class);

        Currency::isCurrency(\stdClass::class);
    }

    public function test_store()
    {
        $repositoryMock = $this->getMockBuilder(CurrencyRepositoryInterface::class)
            ->onlyMethods(['store', 'get'])
            ->getMock();

        $repositoryMock->expects($this->once())->method('store');

        $reflection = new \ReflectionClass(Currency::class);
        $reflection->setStaticPropertyValue('repository', $repositoryMock);

        Currency::store(FakeCurrency::class);
    }

    public function test_get()
    {
        $repositoryMock = $this->getMockBuilder(CurrencyRepositoryInterface::class)
            ->onlyMethods(['get', 'store'])
            ->getMock();

        $repositoryMock->expects($this->once())->method('get');

        $reflection = new \ReflectionClass(Currency::class);
        $reflection->setStaticPropertyValue('repository', $repositoryMock);

        Currency::get(FakeCurrency::class);
    }
}