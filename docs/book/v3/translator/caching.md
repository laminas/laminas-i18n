# Caching

In production, it makes sense to cache your translations.
This not only saves you from loading and parsing the individual formats each time, but also guarantees an optimized loading procedure.

## PSR-6 Support

laminas-i18n supports [PSR-6](https://www.php-fig.org/psr/psr-6/) caching.
[laminas-cache](https://docs.laminas.dev/laminas-cache/psr6/) provides a PSR-6 implementation along with [many other libraries](https://packagist.org/providers/psr/cache-implementation).

> MISSING: **Installation Requirements**
> 
> The following example uses [laminas-cache](https://docs.laminas.dev/laminas-cache/), so make sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-cache
> ```
> 
> laminas-cache is shipped without a specific cache adapter to allow free choice of storage backends and their dependencies.
> So make sure that the required adapters are installed.
>
> The following example uses the [memory adapter of laminas-cache](https://docs.laminas.dev/laminas-cache/storage/adapter/#memory-adapter):
>
> ```bash
> $ composer require laminas/laminas-cache-storage-adapter-memory
> ```

## Enable Caching

To enable caching, pass an instance of `Psr\Cache\CacheItemPoolInterface` to the `setCache()` method.
In combination with laminas-cache, use the `CacheItemPoolDecorator` to wrap the cache adapter:

```php
$translator = new Laminas\I18n\Translator\Translator();
$cache      = new Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator(
    new Laminas\Cache\Storage\Adapter\Memory()
);
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
