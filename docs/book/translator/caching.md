# Caching

In production, it makes sense to cache your translations. This not only saves
you from loading and parsing the individual formats each time, but also
guarantees an optimized loading procedure.

> ### Installation requirements
>
> The cache support of laminas-i18n depends on the laminas-cache component, so
> be sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-cache
> ```

## Enable Caching

To enable caching, pass a `Laminas\Cache\Storage\Adapter` to the `setCache()`
method.

```php
$translator = new Laminas\I18n\Translator\Translator();
$cache      = Laminas\Cache\StorageFactory::factory([
    'adapter' => [
        'name'    => Laminas\Cache\Storage\Adapter\Filesystem::class,
        'options' => [
            'cache_dir' => __DIR__ . '/cache',
        ],
    ],
]);
$translator->setCache($cache);
```

The explanation of creating a cache and and using different adapters for caching
can be found in [documentation of laminas-cache](https://docs.laminas.dev/laminas-cache/).

## Disable Caching

To disable the cache, pass a `null` value to the `setCache()` method.

```php
$translator->setCache(null);
```

## Clear Cache

To clear the cache for a specific text domain and locale, use the `clearCache`
method.

```php
$translator->clearCache('default', 'en_US');
```

## Get Cache Identifier

To get the cache identifier for a specific text domain and locale, use the
`getCacheId`  method:

```php
$translator->getCacheId('default', 'en_US');
```
