<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Vanilium\Money\CurrencyRepository;

final class CurrencyRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function provideCurrencyGetters(): array
    {
        return [
            'By iso' => [
                Resources\FakeCurrency::iso(),
                Resources\FakeCurrency::class,
            ],
            'By sign' => [
                Resources\FakeCurrency::sign(),
                Resources\FakeCurrency::class,
            ],
        ];
    }

    protected function makeFakeCurrency(): \Vanilium\Money\Currency
    {
        return new Resources\FakeCurrency();
    }

    public function test_instance()
    {
        $newInstance = CurrencyRepository::instance();
        $this->assertInstanceOf(CurrencyRepository::class, $newInstance);
    }

    public function test_storage()
    {
        $fakeCurrency = $this->makeFakeCurrency();
        $repository = CurrencyRepository::instance();

        $reflection = new \ReflectionObject($repository);
        $aliasListProperty = $reflection->getProperty('aliasesList');

        $this->assertCount(0, $aliasListProperty->getValue($repository));

        $repository->store(get_class($fakeCurrency));

        $this->assertCount(2, $aliasListProperty->getValue($repository));
        $this->assertArrayHasKey($fakeCurrency::iso(), $aliasListProperty->getValue($repository));
        $this->assertArrayHasKey($fakeCurrency::sign(), $aliasListProperty->getValue($repository));
    }

    /**
     * @depends test_storage
     * @dataProvider provideCurrencyGetters
     */
    public function test_get(string $currencyGetter, string $expectedClass)
    {
        $repository = CurrencyRepository::instance();

        $repository->store($expectedClass);

        $getCurrencyFromRepository = $repository->get($currencyGetter);

        $this->assertIsObject($getCurrencyFromRepository);
        $this->assertInstanceOf($expectedClass, $getCurrencyFromRepository);
    }
}