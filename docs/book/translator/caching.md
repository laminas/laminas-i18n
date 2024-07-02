# Caching

In production, it makes sense to cache your translations. This not only saves
you from loading and parsing the individual formats each time, but also
guarantees an optimized loading procedure.

> MISSING: **Installation Requirements**
> The cache support of laminas-i18n depends on the [laminas-cache](https://docs.laminas.dev/laminas-cache/) component, so be sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-cache
> ```
>
> Version 3 of laminas-cache removed support for factories required by this component, so if your application requires laminas-cache version 3 or later, you will also need to install `laminas-cache-storage-deprecated-factory`
>
> ```bash
> $ composer require laminas/laminas-cache-storage-deprecated-factory
> ```
>
> laminas-cache is shipped without a specific cache adapter to allow free choice of storage backends and their dependencies.
> So make sure that the required adapters are installed.
>
> The following example used the [memory adapter of laminas-cache](https://docs.laminas.dev/laminas-cache/storage/adapter/#memory-adapter):
>
> ```bash
> $ composer require laminas/laminas-cache-storage-adapter-memory
> ```

## Enable Caching

To enable caching, pass a `Laminas\Cache\Storage\Adapter` to the `setCache()`
method.

```php
$translator = new Laminas\I18n\Translator\Translator();
$cache      = Laminas\Cache\StorageFactory::factory([
    'adapter' => [
        'name' => Laminas\Cache\Storage\Adapter\Memory::class,
    ],
]);
$translator->setCache($cache);
```

The explanation of creating a cache and using different adapters for caching
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
