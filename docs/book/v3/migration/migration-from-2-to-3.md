# Migration from Versions 2.x to 3.0

## New Features and Enhancements

### Migration to Container-Driven Caching via Translation Collectors

The `Translator` component no longer manages caching internally, and the legacy `$translator->setCache()` method has been entirely removed.
Caching is now decoupled from the translator itself and is handled architectural-level by wrapping translation lookup routines inside specific Translation Collectors.

When the `AggregateCollector` is resolved from the dependency injection container, an internal delegator factory checks the application configuration for a defined PSR-6 or PSR-16 cache service.
If a valid cache service name is found and successfully retrieved from the container, the factory automatically wraps the `AggregateCollector` in a `CachingCollector`.
This decorator transparently proxies all lookup tasks via `TranslationCollectorInterface::collect()` and caches the results.

#### Configuration Changes

To enable translation caching, the caching service must be registered within the dependency container and point to its service name under the consolidated `laminas-i18n` configuration block.
Either a standard PSR-6 (`psr6_cache`) or a PSR-16 (`psr16_cache`) storage implementation can be provided.

An optional `cache_key_prefix` can also be supplied to prevent key collisions in shared cache environments.

CAUTION: **Type Errors on Direct Injections**
Manual invocation of caching configurations on the translator object or attempting to pass raw legacy `laminas-cache` adapter instances via configuration arrays will result in a `TypeError`.
All caching must be registered as container services.

#### Prior to 3.0.0

```php
$cache = Laminas\Cache\StorageFactory::factory([
    'adapter' => 'filesystem',
]);

$translator = new Laminas\I18n\Translator\Translator();
$translator->setCache($cache);
```

#### Since 3.0.0

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

### Implicit Event Dispatcher Activation

The internal property `eventsEnabled` along with the manual toggle methods `enableEventManager()`, `disableEventManager()`, and `isEventManagerEnabled()` have been completely removed from the `Translator` class.

The `Translator` now implicitly manages events based entirely on the presence of a PSR-14 event dispatcher. If an `EventDispatcherInterface` instance is supplied to the constructor, events (such as `MissingTranslationEvent` or `NoMessagesLoadedEvent`) will automatically be active. If no dispatcher is supplied (or it is `null`), event management remains inactive without causing overhead or requiring code initialization.

In alignment with this architectural change, the `'event_manager_enabled'` configuration option flag has been removed from the factory configuration payload.

#### Prior to 3.0.0

```php
use Laminas\I18n\Translator\Translator;

// 1. Array configuration toggle
$translator = Translator::factory([
    'event_manager_enabled' => true,
]);

// 2. Direct method calls
$translator->enableEventManager();
$translator->disableEventManager();
```

#### Since 3.0.0

```php
use Laminas\I18n\Translator\Translator;

// Events are implicitly ACTIVE because a dispatcher instance is provided to the constructor
$translatorWithEvents = new Translator(
    $collector,
    'en_US',
    null,
    'default',
    $eventDispatcher
);

// Events are implicitly INACTIVE because no dispatcher is present
$translatorWithoutEvents = new Translator(
    $collector,
    'en_US'
);
```

> IMPORTANT: **Migration Action Required**
>
> - Remove the `'event_manager_enabled'` option key from your configuration files and factory option arrays.
> - Remove any direct calls to `enableEventManager()`, `disableEventManager()`, or `isEventManagerEnabled()`.

### Propagation Control for Translator Events

Both `MissingTranslationEvent` and `NoMessagesLoadedEvent` now implement `Psr\EventDispatcher\StoppableEventInterface`.
This allows listeners to stop event propagation by providing a translation or a text domain dynamically.
For `MissingTranslationEvent`, calling `setTranslation()` will automatically stop further propagation, and the provided translation will be used by the translator.

### Introduction of `I18nDefaults`

A new `Laminas\I18n\I18nDefaults` value object has been introduced to encapsulate shared internationalization settings such as default locale, timezone, currency, and country.
This object is registered in the service container and can be used by satellite packages to ensure consistent defaults across the application.

### Default Text Domain Support

The `Translator` now supports a configurable default text domain. This can be set via the `default_text_domain` configuration key under the `translator` block.
If not specified, it continues to default to `default`.

#### Since 3.0.0

```php
return [
    'laminas-i18n' => [
        'translator' => [
            'default_text_domain' => 'my-app-domain',
            // ...
        ],
    ],
];
```

## Structural Changes

### `TextDomain` and Exception Finality

The `Laminas\I18n\Translator\TextDomain` class and all exception classes in `Laminas\I18n\Exception` have been marked as `final`.
This change reinforces the architectural decision to favor composition over inheritance and ensures consistency across the component.

### Interface Namespace Extraction / Consolidation

To better split component responsibilities and streamline the core architecture, core interfaces have been extracted or moved to separate, dedicated packages.

Usages of `Laminas\I18n\Translator\TranslatorInterface` must be replaced with `Laminas\Translator\TranslatorInterface`.

### Extraction of I18n Filters to Satellite Package

The component's built-in filtering capabilities have been extracted into an external standalone package: [`laminas/laminas-i18n-filter`](https://github.com/laminas/laminas-i18n-filter).

The following filter classes are no longer present in `laminas-i18n`:

- `Laminas\I18n\Filter\Alnum`
- `Laminas\I18n\Filter\Alpha`
- `Laminas\I18n\Filter\NumberFormat`
- `Laminas\I18n\Filter\NumberParse`

Additionally, these filters have been reworked to ensure native compatibility with `laminas-filter` v3 execution pipelines.

> IMPORTANT: **Migration Action Required**
> If your application relies on these filters, you must explicitly require the new satellite package:
>
> ```bash
> $ composer require laminas/laminas-i18n-filter
> ```

### Extraction of I18n Validators to Satellite Package

The component's built-in validation capabilities have been extracted into an external standalone package: [`laminas/laminas-i18n-validator`](https://github.com/laminas/laminas-i18n-validator).

The following validator classes are no longer present directly within `laminas-i18n`:

- `Laminas\I18n\Validator\Alnum`
- `Laminas\I18n\Validator\Alpha`
- `Laminas\I18n\Validator\CountryCode`
- `Laminas\I18n\Validator\DateTime`
- `Laminas\I18n\Validator\IsFloat`
- `Laminas\I18n\Validator\IsInt`
- `Laminas\I18n\Validator\PhoneNumber` (Note: Historical wrapper configuration only; see above for full structural replacement details)
- `Laminas\I18n\Validator\PostCode`

Additionally, these extracted validators have been internally refactored to achieve seamless native compatibility with the `laminas-validator` v3 engine and plugin management layers.

> IMPORTANT: **Migration Action Required**
> If your validation chains or forms rely on any of these i18n validation plugins, you must explicitly require the new satellite package via Composer:
>
> ```bash
>  composer require laminas/laminas-i18n-validator
> ```

### Extraction of I18n View Helpers to Satellite Package

The component's built-in view helper capabilities have been extracted into an external standalone package: [`laminas/laminas-i18n-view`](https://github.com/laminas/laminas-i18n-view).

- `Laminas\I18n\View\Helper\CurrencyFormat`
- `Laminas\I18n\View\Helper\DateFormat`
- `Laminas\I18n\View\Helper\NumberFormat`
- `Laminas\I18n\View\Helper\Plural`
- `Laminas\I18n\View\Helper\Translate`
- `Laminas\I18n\View\Helper\TranslatePlural`

Additionally, these extracted view helpers have been internally refactored to support `laminas-view` with version 3.

> IMPORTANT: **Migration Action Required**
> To obtain or retain support from the helpers, the package must also be installed:
>
> ```bash
>  composer require laminas/laminas-i18n-view
> ```

### `Laminas\I18n\Translator\Loader\Ini` Initialization

Due to the removal of `laminas-config`, the `Ini` translator loader no longer implicitly instantiates an internal configuration reader.
It now requires `Laminas\I18n\Translator\Loader\IniFileReader` passed explicitly to its constructor.

NOTE: **Service Container Notice**
If your application resolves the `Ini` translation loader via a service container (such as `Laminas\ServiceManager`), this instantiation change is automatically handled by the component's internal factories.
Manual intervention is only required if you instantiate the loader directly in your code.

#### Prior to 3.0.0

```php
use Laminas\I18n\Translator\Loader\Ini as IniLoader;

$loader = new IniLoader();
```

#### Since 3.0.0

```php
$loader = new Laminas\I18n\Translator\Loader\Ini(
    new Laminas\I18n\Translator\Loader\IniFileReader()
);
```

### Config Namespace Consolidation

To prevent configuration key pollution in global configurations, all translator top-level options must now be nested under the `laminas-i18n` configuration key.

#### Prior to 3.0.0

```php
return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => dirname(__DIR__, 2) . '/data/languages',
                'pattern'  => '%s.php',
            ],
        ],
    ],
];
```

#### Since 3.0.0

```php
return [
    'laminas-i18n' => [
        'translator' => [
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

### Behaviour Changes

The deprecated static constructor `Laminas\I18n\Exception\InvalidArgumentException::withUnknownCountryCode()` has been removed.

## Removed Features

### Drop MVC Support (`Laminas\I18n\Module` Removal)

The MVC `Module` class has been entirely removed from the package.

CAUTION: **No Migration Path for MVC Applications**
Integration with `laminas-mvc` is completely dropped.
There is no official upgrade path for legacy MVC applications because `laminas-mvc` will not be updated to support ServiceManager v4.
Consequently, version 3.0+ of this component cannot be installed alongside the core MVC framework stack.

### Removed Dependencies

#### `laminas/laminas-config`

The dependency on `laminas/laminas-config` has been removed. The component now utilizes a built-in INI file reader implementation instead.

### Removed Classes

#### `Laminas\I18n\Validator\PhoneNumber`

The deprecated phone number validator has been removed in favour of the standalone library [`laminas-i18n-phone-number`](https://github.com/laminas/laminas-i18n-phone-number).

In most cases, the phone number library can be used as a direct replacement by using `Laminas\I18n\PhoneNumber\Validator\PhoneNumber`.

### Removed Exceptions

The following unused exception classes have been removed:

- `Laminas\I18n\Exception\OutOfBoundsException`
- `Laminas\I18n\Exception\ExtensionNotLoadedException`
