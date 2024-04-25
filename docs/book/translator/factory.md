# Factory

## Setting Locale

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'locale' => 'de_DE',
]);
```

## Setting Fallback Locale

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'locale' => [
        'de_DE', // Default locale
        'en_GB', // Fallback locale
    ],
]);
```

## Setting Translation File Patterns

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'translation_file_patterns' => [
        [
            'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
            'base_dir' => __DIR__ . '/languages',
            'pattern'  => '%s.php',
        ],
        [
            'type'        => Laminas\I18n\Translator\Loader\PhpArray::class,
            'base_dir'    => __DIR__ . '/languages',
            'pattern'     => 'album-%s.php',
            'text_domain' => 'album',
        ],
    ],
]);
```

Each file pattern option array must contain `type`, `base_dir` and `pattern`.
The option for `text_domain` is optional. The default value for `text_domain` is
`default`.

## Setting Translation Files

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'translation_files' => [
        [
            'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
            'filename' => __DIR__ . '/languages/en_GB.php',
        ],
        [
            'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
            'filename' => __DIR__ . '/languages/de_DE.php',
            'locale'   => 'de_DE',
        ],
        [
            'type'        => Laminas\I18n\Translator\Loader\PhpArray::class,
            'filename'    => __DIR__ . '/languages/album-de_DE.php',
            'locale'      => 'de_DE',
            'text_domain' => 'album',
        ],
    ],
]);
```

Each file option array must contain `type` and `filename`. The options for
`locale` and the `text_domain` are optional. The default value for `locale` is
`null` and for `text_domain` it is `default`.

## Setting Remote Translations

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'remote_translation' => [
        [
            'type' => 'translation-de_DE', // Custom name
        ],
        [
            'type'        => 'translation-de_DE', // Custom name
            'text_domain' => 'album',
        ],
    ],
]);
```

Each remote option array must contain `type`. The option for `text_domain` is
optional. The default value for `text_domain` is `default`.

### Adding Translations

```php
$translator->getPluginManager()->setService(
    'translation-de_DE', // Custom name
    new \Laminas\I18n\Translator\Loader\PhpMemoryArray([
        'default' => [
            'de_DE' => [
                'car'   => 'Auto',
                'train' => 'Zug',
            ],
        ],
        'album'   => [
            'de_DE' => [
                'music' => 'Musik',
            ],
        ],
    ])
);
```

## Setting Cache

### Using a Cache Instance

The following example is based on the use of the
[laminas-cache](https://docs.laminas.dev/laminas-cache/) component.

```php
$translator = new Laminas\I18n\Translator\Translator();
$cacheStorage = new Laminas\Cache\Storage\Adapter\Filesystem([
    'cache_dir' => __DIR__ . '/cache',
]);
$cache = new Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator($cacheStorage);
$translator = Laminas\I18n\Translator\Translator::factory([
    'cache' => $cache,
]);
```

## Enable EventManager

```php
$translator = Laminas\I18n\Translator\Translator::factory([
    'event_manager_enabled' => true,
]);
```
