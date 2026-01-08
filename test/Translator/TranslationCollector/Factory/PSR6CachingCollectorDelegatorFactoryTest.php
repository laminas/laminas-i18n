<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector\Factory;

use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\TranslationCollector\Factory\PSR6CachingCollectorDelegatorFactory;
use Laminas\I18n\Translator\TranslationCollector\PSR6CachingCollector;
use LaminasTest\I18n\Translator\TranslationCollector\FixedCollector;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

final class PSR6CachingCollectorDelegatorFactoryTest extends TestCase
{
    private CacheItemPoolInterface $cache;

    protected function setUp(): void
    {
        $this->cache = new CacheItemPoolDecorator(new Memory());
    }

    public function testExceptionThrownWhenDelegatedServiceIsNotACollector(): void
    {
        $factory = new PSR6CachingCollectorDelegatorFactory();
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

        $factory = new PSR6CachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertSame($collector, $result);
    }

    public function testInvalidCacheServicesAreIgnored(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSR6CachingCollectorDelegatorFactory();

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

    public function testServiceIsDelegatedWhenCacheServiceIsAvailable(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSR6CachingCollectorDelegatorFactory();

        $result = $factory->__invoke(
            TestHelper::containerWithConfig([
                'laminas-i18n' => [
                    'translator' => [
                        'psr6_cache' => 'ServiceName',
                    ],
                ],
                'dependencies' => [
                    'services' => [
                        'ServiceName' => $this->cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );

        self::assertNotSame($collector, $result);
        self::assertInstanceOf(PSR6CachingCollector::class, $result);
    }

    public function testCachePrefixCanBeCustomised(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSR6CachingCollectorDelegatorFactory();

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
                        'ServiceName' => $this->cache,
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

    public function testExceptionThrownForInvalidCacheKeyPrefix(): void
    {
        $collector = FixedCollector::make();

        $factory = new PSR6CachingCollectorDelegatorFactory();

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
                        'ServiceName' => $this->cache,
                    ],
                ],
            ]),
            'foo',
            static fn(): FixedCollector => $collector,
        );
    }
}
