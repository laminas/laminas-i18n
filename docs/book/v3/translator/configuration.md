# Configuration

## Configuration Key

The configuration key for the translator is `translator` under the configuration block `laminas-i18n`.

```php
return [
    'laminas-i18n' => [
        'translator' => [
            // …
        ],
    ],
];
```

## General Options

The following options are available at the top-level of the configuration:

- `locale`: The default locale to use. When `null` (default), it uses `Locale::getDefault()`.
- `timezone`: **Deprecated**. Use `laminas-i18n.defaultTimeZone` instead.

### laminas-i18n Options

Under the `laminas-i18n` key, you can configure the following defaults:

- `defaultTimeZone`: The default timezone. If `null`, it uses `date_default_timezone_get()`.
- `defaultCurrency`: The default currency code (e.g., `USD`, `EUR`). If not set, it's extrapolated from the locale.
- `defaultTextDomain`: The default text domain for translations. Defaults to `default`.
- `defaultCountry`: The default country code (e.g., `US`, `DE`). If not set, it's extrapolated from the locale.

### Loader Options

You can configure default options for specific translation file loaders:

- `ini-format-options`:
    - `process-sections`: Whether to process sections in INI files. Defaults to `true`.
    - `typed`: Whether to use typed values. Defaults to `false`.
    - `nest-separator`: The separator for nested keys. Defaults to `.`.
- `gettext-loader-options`:
    - `use-include-path`: Whether to use the PHP include path. Defaults to `false`.
- `php-loader-options`:
    - `use-include-path`: Whether to use the PHP include path. Defaults to `false`.

## Translator Options

The `translator` block under `laminas-i18n` supports the following options:

- `translation_files`: A list of specific translation files to load.
- `translation_file_patterns`: A list of patterns to load multiple translation files from a directory.
- `remote_translation`: Configuration for remote translation loaders.
- `aggregate_collector`: A list of collector class names to use.
- `fallback_locale`: A fallback locale for when the default locale has no translations.
- `psr6_cache`: The service ID of a PSR-6 cache item pool.
- `psr16_cache`: The service ID of a PSR-16 cache. If both PSR-6 and PSR-16 are defined, PSR-6 is preferred.
- `cache_key_prefix`: A custom prefix for cache keys. Defaults to `LaminasTranslations`.

## Adding Translation Files

To add translation files, you can follow these steps:

1. Create a new directory for your translations.
2. Inside the directory, create a file for each language you want to support.
3. In each language file, define the translations for the strings you want to translate.
4. Configure the translator to use the new directory and language files.

### File List

To add a list of translation files, use the `translation_files` key:

```php
'laminas-i18n' => [
    'translator' => [
        'translation_files' => [
            [
                'type'        => 'phpArray',
                'filename'    => 'path/to/translations/en_US.php',
                'locale'      => 'en_US',
                'text_domain' => 'default',
            ],
            // more entries…
        ],
    ],
],
```

- `type`: Class name or alias of a `Laminas\I18n\Translator\Loader\FileLoaderInterface`.
- `filename`: The path to the file to load.
- `locale`: (Optional) The locale for this file. Defaults to the default locale.
- `text_domain`: (Optional) The text domain for this file. Defaults to the default text domain.

### File Patterns

To add translation files using a pattern, use the `translation_file_patterns` key:

```php
'laminas-i18n' => [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'        => 'gettext',
                'base_dir'    => 'path/to/translations',
                'pattern'     => '%s.mo',
                'text_domain' => 'default',
            ],
            // more entries…
        ],
    ],
],
```

- `type`: Class name or alias of a `Laminas\I18n\Translator\Loader\FileLoaderInterface`.
- `base_dir`: The directory where the files are located.
- `pattern`: The file name pattern in `printf` format (e.g., `%s.mo`).
- `text_domain`: (Optional) The text domain. Defaults to the default text domain.

### Remote Translations

To add remote translations, use the `remote_translation` key:

```php
'laminas-i18n' => [
    'translator' => [
        'remote_translation' => [
            [
                'type'        => 'MyRemoteLoader',
                'text_domain' => 'default',
            ],
        ],
    ],
],
```

- `type`: Class name or alias of a `Laminas\I18n\Translator\Loader\RemoteLoaderInterface`.
- `text_domain`: (Optional) The text domain. Defaults to the default text domain.

## Event Dispatcher

Events are automatically handled based on whether a PSR-14 `Psr\EventDispatcher\EventDispatcherInterface` implementation is passed to the Translator constructor.

```php
return [
    'dependencies' => [
        'factories' => [
            // Register PSR-14 compliant event dispatcher implementation
            Psr\EventDispatcher\EventDispatcherInterface::class => MyEventDispatcherFactory::class,
        ],
    ],
];
```
