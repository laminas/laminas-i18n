<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector\Factory;

use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\TranslationCollector\Factory\PSRCachingCollectorDelegatorFactory;
use Laminas\I18n\Translator\TranslationCollector\PSR16CachingCollector;
use Laminas\I18n\Translator\TranslationCollector\PSR6CachingCollector;
use LaminasTest\I18n\Translator\TranslationCollector\FixedCollector;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

final class PSRCachingCollectorDelegatorFactoryTest extends TestCase
{
    private CacheItemPoolInterface $psr6cache;
    private CacheInterface $psr16cache;

    protected function setUp(): void
    {
        $adapter          = new Memory();
        $this->psr6cache  = new CacheItemPoolDecorator($adapter);
        $this->psr16cache = new SimpleCacheDecorator($adapter);
    }

    public function testExceptionThrownWhenDelegatedServiceIsNotACollector(): void
    {
        $factory = new PSRCachingCollectorDelegatorFactory();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Expected the delegated service to be a translation collector but received "string"',
        );
        $factory->__invoke(
            $this->createStub(ContainerInterface::class),
            'foo',
            static fn(): string => 'Foo',
        );
    }

    public function testWhenTheCacheServiceIsNotConfiguredTheCollectorIsNotDelegated(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertSame($collector, $result);
    }

    public function testInvalidPSR6CacheServicesAreIgnored(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache' => 'ServiceName',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => 'Not a cache',
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertSame($collector, $result);
    }

    public function testInvalidPSR16CacheServicesAreIgnored(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr16_cache' => 'ServiceName',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => 'Not a cache',
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertSame($collector, $result);
    }

    public function testServiceIsDelegatedWhenPSR6CacheServiceIsAvailable(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache' => 'ServiceName',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->psr6cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR6CachingCollector::class, $result);
    }

    public function testServiceIsDelegatedWhenPSR16CacheServiceIsAvailable(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr16_cache' => 'ServiceName',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->psr16cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR16CachingCollector::class, $result);
    }

    public function testPSR6DecoratorIsUsedWhenBothAreDefined(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache'  => 'PSR6',
                        'psr16_cache' => 'PSR16',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'PSR6'  => $this->psr6cache,
                        'PSR16' => $this->psr16cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR6CachingCollector::class, $result);
    }

    public function testPSR6CachePrefixCanBeCustomised(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache'       => 'ServiceName',
                        'cache_key_prefix' => 'Goats',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->psr6cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR6CachingCollector::class, $result);

        self::assertSame(
            'Goats-default-en_GB',
            $result->cacheKey('default', 'en_GB'),
        );
    }

    public function testPSR16CachePrefixCanBeCustomised(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr16_cache'      => 'ServiceName',
                        'cache_key_prefix' => 'Goats',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->psr16cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR16CachingCollector::class, $result);

        self::assertSame(
            'Goats-default-en_GB',
            $result->cacheKey('default', 'en_GB'),
        );
    }

    public function testExceptionThrownForInvalidCacheKeyPrefix(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSRCachingCollectorDelegatorFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The `cache_key_prefix` option must resolve to null or a non-empty-string');
        $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache'       => 'ServiceName',
                        'cache_key_prefix' => 123,
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->psr6cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );
    }
}
