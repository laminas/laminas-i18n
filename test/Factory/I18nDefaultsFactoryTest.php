<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Factory;

use ArrayObject;
use Laminas\I18n\ConfigProvider;
use Laminas\I18n\Factory\I18nDefaultsFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Traversable;

use function array_replace_recursive;
use function date_default_timezone_get;
use function iterator_to_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class I18nDefaultsFactoryTest extends TestCase
{
    /** @return list<array{0: string, 1: string, 2: string}> */
    public static function localesAndDefaultDetectionsProvider(): array
    {
        return [
            ['en_GB', 'GBP', 'GB'],
            ['en_US', 'USD', 'US'],
            ['sw_TZ', 'TZS', 'TZ'],
            ['sw_ZA', 'ZAR', 'ZA'],
            ['de_DE', 'EUR', 'DE'],
            ['es_MX', 'MXN', 'MX'],
            ['es_AR', 'ARS', 'AR'],
            ['es_UY', 'UYU', 'UY'],
            ['pt_BR', 'BRL', 'BR'],
            ['pt_PT', 'EUR', 'PT'],
        ];
    }

    #[DataProvider('localesAndDefaultDetectionsProvider')]
    public function testCorrectCountryAndCurrencyIsDetectedFromConfiguredLocale(
        string $locale,
        string $expectCurrency,
        string $expectCountry,
    ): void {
        $container = $this->containerWithConfig(['locale' => $locale]);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame($locale, $defaults->defaultLocale);
        self::assertSame($expectCurrency, $defaults->defaultCurrencyCode);
        self::assertSame($expectCountry, $defaults->defaultCountry->toString());
    }

    /** @return iterable<string, array{0: iterable|null, 1: string}> */
    public static function timezoneConfigScenarios(): iterable
    {
        $system = date_default_timezone_get();
        $values = [
            'No config at all'          => [null, $system],
            'Empty config'              => [[], $system],
            'Legacy Key: Null value'    => [['timezone' => null], $system],
            'Legacy Key: Empty string'  => [['timezone' => ''], $system],
            'Legacy Key: Non-empty'     => [['timezone' => 'Europe/London'], 'Europe/London'],
            'New Key: Null Value'       => [['laminas-i18n' => ['defaultTimeZone' => null]], $system],
            'New Key: Empty String'     => [['laminas-i18n' => ['defaultTimeZone' => '']], $system],
            'New Key: Not Empty'        => [
                [
                    'laminas-i18n' => ['defaultTimeZone' => 'Australia/Adelaide'],
                ],
                'Australia/Adelaide',
            ],
            'New Key overrides old key' => [
                [
                    'timezone'     => 'Africa/Johannesburg',
                    'laminas-i18n' => ['defaultTimeZone' => 'Australia/Adelaide'],
                ],
                'Australia/Adelaide',
            ],
        ];

        yield from $values;

        foreach ($values as $key => $args) {
            if ($args[0] === null) {
                continue;
            }

            $args[0] = new ArrayObject($args[0]);

            yield $key . ' (ArrayObject)' => $args;
        }
    }

    #[DataProvider('timezoneConfigScenarios')]
    public function testTimeZoneWithVariousConfigurations(iterable|null $config, string $expect): void
    {
        $container = $this->containerWithConfig($config);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame($expect, $defaults->defaultTimeZone->getName());
    }

    /** @return list<array{0: string, 1: string, 2: string}> */
    public static function explicitCurrencyProvider(): array
    {
        return [
            ['en_GB', 'GBP', 'BWP'],
            ['en_US', 'USD', 'BWP'],
            ['sw_TZ', 'TZS', 'BWP'],
            ['sw_ZA', 'ZAR', 'BWP'],
            ['de_DE', 'EUR', 'BWP'],
            ['es_MX', 'MXN', 'BWP'],
            ['es_AR', 'ARS', 'BWP'],
            ['es_UY', 'UYU', 'BWP'],
            ['pt_BR', 'BRL', 'BWP'],
            ['pt_PT', 'EUR', 'BWP'],
        ];
    }

    #[DataProvider('explicitCurrencyProvider')]
    public function testExplicitCurrencySettingOverridesDetectionFromLocale(
        string $locale,
        string $localeCurrency,
        string $configuredCurrency,
    ): void {
        $container = $this->containerWithConfig([
            'locale'       => $locale,
            'laminas-i18n' => [
                'defaultCurrency' => $configuredCurrency,
            ],
        ]);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame($configuredCurrency, $defaults->defaultCurrencyCode);
        self::assertNotSame($localeCurrency, $defaults->defaultCurrencyCode);
    }

    /** @return list<array{0: string, 1: string, 2: string}> */
    public static function explicitCountryProvider(): array
    {
        return [
            ['en_GB', 'GB', 'CA'],
            ['en_US', 'US', 'CA'],
            ['sw_TZ', 'TZ', 'CA'],
            ['sw_ZA', 'ZA', 'CA'],
            ['de_DE', 'DE', 'CA'],
            ['es_MX', 'MX', 'CA'],
            ['es_AR', 'AR', 'CA'],
            ['es_UY', 'UY', 'CA'],
            ['pt_BR', 'BR', 'CA'],
            ['pt_PT', 'PT', 'CA'],
        ];
    }

    #[DataProvider('explicitCountryProvider')]
    public function testExplicitCountrySettingOverridesDetectionFromLocale(
        string $locale,
        string $localeCountry,
        string $configuredCountry,
    ): void {
        $container = $this->containerWithConfig([
            'locale'       => $locale,
            'laminas-i18n' => [
                'defaultCountry' => $configuredCountry,
            ],
        ]);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame($configuredCountry, $defaults->defaultCountry->toString());
        self::assertNotSame($localeCountry, $defaults->defaultCountry->toString());
    }

    public function testThatAWonkyLocaleWillNotFailToDetectACurrencyOrCountry(): void
    {
        $container = $this->containerWithConfig([
            'locale' => 'ab_XX',
        ]);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame('XXX', $defaults->defaultCurrencyCode);
        self::assertSame('XX', $defaults->defaultCountry->toString());
    }

    public function testDefaultTextDomain(): void
    {
        $container = $this->containerWithConfig(null);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame('default', $defaults->defaultTextDomain);
    }

    public function testDefaultTextDomainCanBeConfigured(): void
    {
        $container = $this->containerWithConfig([
            'laminas-i18n' => [
                'defaultTextDomain' => 'rabbits',
            ],
        ]);
        $factory   = new I18nDefaultsFactory();
        $defaults  = $factory->__invoke($container, 'whatever');

        self::assertSame('rabbits', $defaults->defaultTextDomain);
    }

    private function containerWithConfig(iterable|null $config): ContainerInterface
    {
        $useObject = $config instanceof Traversable;
        $config    = array_replace_recursive(
            (new ConfigProvider())->__invoke(),
            $config === null ? [] : iterator_to_array($config),
        );

        /** @psalm-var ServiceManagerConfiguration $deps */
        $deps             = $config['dependencies'];
        $config           = $useObject ? new ArrayObject($config) : $config;
        $deps['services'] = ['config' => $config];

        return new ServiceManager($deps);
    }
}
