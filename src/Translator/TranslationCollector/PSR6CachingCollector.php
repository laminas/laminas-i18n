<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\TextDomain;
use Psr\Cache\CacheItemPoolInterface;

use function get_debug_type;
use function sprintf;

final readonly class PSR6CachingCollector implements CachingCollectorInterface
{
    private const DEFAULT_PREFIX = 'LaminasTranslations';
    /** @var non-empty-string */
    private string $keyPrefix;

    /** @param non-empty-string|null $keyPrefix */
    public function __construct(
        private CacheItemPoolInterface $cache,
        private TranslationCollectorInterface $collector,
        string|null $keyPrefix = null,
    ) {
        $this->keyPrefix = $keyPrefix ?? self::DEFAULT_PREFIX;
    }

    public function collect(string $textDomain, string $locale): TextDomain
    {
        $key  = $this->cacheKey($textDomain, $locale);
        $item = $this->cache->getItem($key);
        if ($item->isHit()) {
            return $this->assertTextDomain($key, $item->get());
        }

        $data = $this->collector->collect($textDomain, $locale);
        $item->set($data);
        $this->cache->save($item);

        return $data;
    }

    public function cacheKey(string $textDomain, string $locale): string
    {
        return sprintf('%s-%s-%s', $this->keyPrefix, $textDomain, $locale);
    }

    public function clearCache(string $textDomain, string $locale): void
    {
        $this->cache->deleteItem($this->cacheKey($textDomain, $locale));
    }

    private function assertTextDomain(string $key, mixed $value): TextDomain
    {
        if (! $value instanceof TextDomain) {
            throw new RuntimeException(sprintf(
                'Expected the cache key "%s" to contain translation messages but it was "%s"',
                $key,
                get_debug_type($value),
            ));
        }

        return $value;
    }
}
