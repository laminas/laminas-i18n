<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

interface CachingCollectorInterface extends TranslationCollectorInterface
{
    /**
     * Return the cache item key for a text-domain and locale pair
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @return non-empty-string
     */
    public function cacheKey(string $textDomain, string $locale): string;

    /**
     * Remove the translations identified by text domain and locale from the cache
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     */
    public function clearCache(string $textDomain, string $locale): void;
}
