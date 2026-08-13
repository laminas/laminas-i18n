# Caching

In production, it makes sense to cache your translations.
This not only saves you from loading and parsing the individual formats each time, but also guarantees an optimized loading procedure.

## PSR-6 and PSR-16 Support

laminas-i18n supports [PSR-6](https://www.php-fig.org/psr/psr-6/) and [PSR-16](https://www.php-fig.org/psr/psr-16/) caching.
[laminas-cache](https://docs.laminas.dev/laminas-cache/) provides a PSR-6 and a PSR-16 implementation along with [many other libraries](https://packagist.org/providers/psr/cache-implementation).

## Configure Caching

Set the cache service to use for caching translations.
Use the `psr6_cache` **or** `psr16_cache` key to set the cache service.

```php
return [
    'dependencies' => [
        'factories' => [
            // Register the PSR-6 or PSR-16 compliant cache service factory
            'MyApp\CacheService' => MyApp\Cache\CacheFactory::class,
        ],
    ],
    'laminas-i18n' => [
        'translator' => [
            // Point to the service container key
            'psr6_cache'       => 'MyApp\CacheService',
            // Alternatively, use a PSR-16 cache provider:
            // 'psr16_cache'    => 'MyApp\SimpleCacheService',

            // Optional: Customize the translation cache isolation
            'cache_key_prefix' => 'app_translation_',

            'translation_file_patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => dirname(__DIR__, 2) . '/data/languages',
                    'pattern'  => '%s.php',
                ],
            ],
        ],
    ],
];
```

NOTE: If both `psr6_cache` and `psr16_cache` are set, the PSR-6 cache will be preferred.

## Preparing to Access the Cache Features

The caching is decoupled from the translator itself.
The translation collector is responsible for caching translations.
To fetch the defined translation collector, use the `TranslationCollectorInterface` service:

```php
/** @var Laminas\I18n\Translator\TranslationCollector\PSR6CachingCollector|Laminas\I18n\Translator\TranslationCollector\PSR16CachingCollector $collector */
$collector = $container->get(
    Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface::class
);
```

If the cache is configured, this will return a decorated translation collector instance.

## Clear Cache

To clear the cache for a specific text domain and locale, use the `clearCache` method.

```php
$collector->clearCache('default', 'en_US');
```

## Get Cache Identifier

To get the cache identifier for a specific text domain and locale, use the `cacheKey` method:

```php
$collector->cacheKey('default', 'en_US');
```
