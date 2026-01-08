<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\TranslationCollector\PSR6CachingCollector;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

final class PSR6CachingCollectorTest extends TestCase
{
    private CacheItemPoolInterface $cache;
    private FixedCollector $collector;

    protected function setUp(): void
    {
        $this->cache     = new CacheItemPoolDecorator(new Memory());
        $this->collector = new FixedCollector(new TextDomain([
            'Message' => 'Translation',
        ]));
    }

    public function testCollectedMessagesAreCached(): void
    {
        $collector = new PSR6CachingCollector(
            $this->cache,
            $this->collector,
        );

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        self::assertFalse($item->isHit());

        $result = $collector->collect('default', 'en_GB');
        self::assertSame('Translation', $result['Message']);

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        self::assertTrue($item->isHit());

        $cached = $item->get();
        self::assertInstanceOf(TextDomain::class, $cached);
        self::assertSame('Translation', $cached['Message']);

        self::assertSame($cached, $collector->collect('default', 'en_GB'));
    }

    public function testCollectedMessagesCanBeCleared(): void
    {
        $collector = new PSR6CachingCollector(
            $this->cache,
            $this->collector,
        );

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        self::assertFalse($item->isHit());

        $collector->collect('default', 'en_GB');

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        self::assertTrue($item->isHit());

        $collector->clearCache('default', 'en_GB');

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        self::assertFalse($item->isHit());
    }

    public function testExceptionThrownWhenCachedItemIsNotATextDomainInstance(): void
    {
        $collector = new PSR6CachingCollector(
            $this->cache,
            $this->collector,
        );

        $item = $this->cache->getItem($collector->cacheKey('default', 'en_GB'));
        $item->set('Whatever');
        $this->cache->save($item);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Expected the cache key "LaminasTranslations-default-en_GB" '
            . 'to contain translation messages but it was "string"',
        );

        $collector->collect('default', 'en_GB');
    }

    public function testTheCacheKeyPrefixCanBeCustomised(): void
    {
        $collector = new PSR6CachingCollector(
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
