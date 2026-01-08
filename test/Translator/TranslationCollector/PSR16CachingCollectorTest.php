<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\TranslationCollector\PSR16CachingCollector;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

final class PSR16CachingCollectorTest extends TestCase
{
    private CacheInterface $cache;
    private FixedCollector $collector;

    protected function setUp(): void
    {
        $this->cache     = new SimpleCacheDecorator(new Memory());
        $this->collector = new FixedCollector(new TextDomain([
            'Message' => 'Translation',
        ]));
    }

    public function testCollectedMessagesAreCached(): void
    {
        $collector = new PSR16CachingCollector(
            $this->cache,
            $this->collector,
        );

        $item = $this->cache->get($collector->cacheKey('default', 'en_GB'));
        self::assertNull($item);

        $result = $collector->collect('default', 'en_GB');
        self::assertSame('Translation', $result['Message']);

        $cached = $this->cache->get($collector->cacheKey('default', 'en_GB'));
        self::assertInstanceOf(TextDomain::class, $cached);

        self::assertSame('Translation', $cached['Message']);

        self::assertSame($cached, $collector->collect('default', 'en_GB'));
    }

    public function testCollectedMessagesCanBeCleared(): void
    {
        $collector = new PSR16CachingCollector(
            $this->cache,
            $this->collector,
        );

        $cacheKey = $collector->cacheKey('default', 'en_GB');

        $item = $this->cache->get($cacheKey);
        self::assertNull($item);

        $collector->collect('default', 'en_GB');

        self::assertTrue($this->cache->has($cacheKey));

        $collector->clearCache('default', 'en_GB');

        self::assertFalse($this->cache->has($cacheKey));
    }

    public function testExceptionThrownWhenCachedItemIsNotATextDomainInstance(): void
    {
        $collector = new PSR16CachingCollector(
            $this->cache,
            $this->collector,
        );

        $cacheKey = $collector->cacheKey('default', 'en_GB');
        $this->cache->set($cacheKey, 'Whatever');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Expected the cache key "LaminasTranslations-default-en_GB" '
            . 'to contain translation messages but it was "string"',
        );

        $collector->collect('default', 'en_GB');
    }

    public function testTheCacheKeyPrefixCanBeCustomised(): void
    {
        $collector = new PSR16CachingCollector(
            $this->cache,
            $this->collector,
            'Kermit',
        );

        self::assertSame(
            'Kermit-default-en_GB',
            $collector->cacheKey('default', 'en_GB'),
        );
    }
}
