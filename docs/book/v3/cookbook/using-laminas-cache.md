# Using laminas-cache for Caching

The following cookbook covers using [laminas-cache](https://docs.laminas.dev/laminas-cache/) for caching.

> MISSING: **Installation Requirements**
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
>
> By using the [laminas-component-installer](https://docs.laminas.dev/laminas-component-installer/) the config-providers for laminas-cache and the memory adapter are automatically injected into the dependency-injection container.

## Define the Cache Service

The standard cache must be decorated with the `CacheItemPoolDecorator` to provide the `Psr\Cache\CacheItemPoolInterface` interface (PSR-6) or with the `SimpleCacheDecorator` to provide the `Psr\SimpleCache\CacheInterface` interface (PSR-16).

```php
$cachePluginManager = $container->get(
    Laminas\Cache\Storage\AdapterPluginManager::class
);
$container->setService(
    'MyApp\CacheService',
    new Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator(
        $cachePluginManager->get(
            Laminas\Cache\Storage\Adapter\Memory::class
        )
    )
);
```

## Preparing to Access the Cache Features

The caching is decoupled from the translator itself.
The translation collector is responsible for caching translations.
To fetch the _general_ translation collector, use the `TranslationCollectorInterface` service:

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

<!-- markdownlint-disable no-inline-html -->
<details><summary>Stand-Alone Usage</summary>

<h3>Configure the Dependency Injection Container</h3>

Configure the dependency-injection container with the help of the config provider and add the translation file and set the cache name via configuration:

```php
$container = (new Laminas\ServiceManager\ServiceManager(
    (new Laminas\I18n\ConfigProvider())->getDependencyConfig()
));
```

```php
$container->setService(
    'config',
    [
        'laminas-i18n' => [
            'translator' => [
                'psr6_cache'        => 'MyApp\CacheService',
                // or 'psr16_cache'       => 'MyApp\CacheService',
                'translation_files' => [
                    [
                        'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
                        'filename' => __DIR__ . '/languages/de_DE.php',
                        'locale'   => 'de_DE',
                    ],
                ],
            ],
        ],
    ]
);
```

<h4>Configure the Cache Adapter</h4>

Extend the dependency-injection container with the help of the <a href="https://docs.laminas.dev/laminas-config-aggregator/config-providers/">config providers</a> to add laminas-cache and cache adapter:

```php
$container->configure(
    (new Laminas\Cache\ConfigProvider())->getDependencyConfig()
);
$container->configure(
    (new Laminas\Cache\Storage\Adapter\Memory\ConfigProvider(
    ))->getServiceDependencies()
);
```

The next steps are the same as before.

</details>
<!-- markdownlint-enable no-inline-html -->

## More To Read

- [Creating a cache and using different adapters with laminas-cache](https://docs.laminas.dev/laminas-cache/)
- [Stand-alone usage of laminas-i18n](../application-integration/stand-alone.md)
