# Introduction

`laminas-i18n` comes with a complete translation suite supporting all major
formats and including popular features such as plural translations and text
domains.

The component is entirely configuration-driven and designed to work seamlessly
within a dependency injection environment. Instead of manual instantiation,
instances should be fetched directly from a PSR-11 compatible service container
(such as `laminas-servicemanager`).

## Adding Translations

Translations are integrated into the translator via your application configuration files (e.g., `config/autoload/i18n.global.php`).

The translator is driven by an underlying `AggregateCollector` (which may be transparently wrapped in a `CachingCollector` if a cache service is configured). This aggregate collector orchestrates multiple specialized sub-collectors simultaneously:

- `FileListCollector`: Inspects explicitly declared individual files.
- `FilePatternCollector`: Scans directories using global matching layout patterns.
- `RemoteListCollector`: Coordinates lookups from remote databases or API endpoints (available if custom remote loaders are registered).

When a translation lookup occurs, all active collectors are consulted in parallel, and their translation assets are merged into a unified dataset. You can configure any combination of these strategies concurrently within your `laminas-i18n` configuration block.

### Configuration via Explicit Files (FileListCollector)

To map single file targets manually file-by-file, utilize the `translation_files` configuration key. This is ideal for one-off locales, complex directory structures that layout patterns cannot easily scan, or assets isolated to specific text domains.

```php
return [
    'laminas-i18n' => [
        'translator' => [
            'translation_files' => [
                [
                    'type'        => 'phparray',
                    'filename'    => dirname(__DIR__, 2) . '/data/languages/en_US.php',
                    'locale'      => 'en_US',
                ],
            ],
        ],
    ],
];

```

- `type`: The identifier of the format loader to use (e.g., `phparray`, `gettext`, `ini`).
- `filename`: The absolute path to the file containing your translation keys.
- `locale`: The language locale target this file fulfills. This is required for flat formats hosting single-locale keys.
- `text_domain`: An optional "category" name to isolate translations by context. If omitted, it defaults to `"default"`.

### Configuration via File Patterns (FilePatternCollector)

When managing a modular dictionary structure where each locale owns its own distinct file, use the `translation_file_patterns` array. This enables your application to scale cleanly; dropping a new locale file into the target directory automatically makes it available without modifying any code or configuration arrays.

```php
return [
    'laminas-i18n' => [
        'translator' => [
            'translation_file_patterns' => [
                [
                    'type'        => 'phparray',
                    'base_dir'    => dirname(__DIR__, 2) . '/data/languages',
                    'pattern'     => '%s.php',
                ],
            ],
        ],
    ],
];

```

- `base_dir`: The root directory containing your structured translation file tree.
- `pattern`: An `sprintf()`-compliant formatting string outlining how to locate files under the `base_dir`. It must include a string substitution placeholder (`%s` or `%1$s`) which represents the active `locale` being requested. For example, if your translation files reside at `/var/messages/{locale}/messages.mo`, your pattern string must be `/var/messages/%s/messages.mo`.
- `text_domain`: An optional contextual category name. Defaults to `"default"`.

### Configuration via Remote Assets (RemoteListCollector)

If you serve translation values dynamically from a database table, a Redis instance, or an external translation API, you can supply a `remote_translation` array.

NOTE: **Custom Implementations Required**
The `RemoteListCollector` is configured and ready to execute out-of-the-box, but the core component does not bundle default, concrete remote loaders. You must implement `Laminas\I18n\Translator\Loader\RemoteLoaderInterface` and register your custom service key.

```php
return [
    'laminas-i18n' => [
        'translator' => [
            'remote_translation' => [
                [
                    'type'        => 'MyCustomDatabaseLoader',
                ],
            ],
        ],
    ],
];

```

- `type`: The identifier or class name of the custom remote format loader registered within the `MessageLoaderPluginManagerInterface`. It must resolve to a non-empty string.
- `text_domain`: A contextual category name used to isolate or segment these remote translations. If this is omitted, it automatically falls back to your application's configured default text domain (via `I18nDefaults`). It must resolve to a non-empty string.

## Container Retrieval

To obtain the fully initialized and configured `Translator` instance, retrieve
it directly from your DI container:

```php
/** @var Psr\Container\ContainerInterface $container */
$translator = $container->get(Laminas\I18n\Translator\Translator::class);
```

## Supported formats

The translator supports the following major translation formats:

- PHP arrays
- Gettext
- INI

Additionally, you can use custom formats by implementing one or more of
`Laminas\I18n\Translator\Loader\FileLoaderInterface` or
`Laminas\I18n\Translator\Loader\RemoteLoaderInterface`, and registering your loader
with the `Translator` instance's composed plugin manager.

## Setting a locale

By default, the translator will get the locale to use from ext/intl's `Locale`
class. If you want to set an alternative locale explicitly, you can do so by
passing it to the `setLocale()` method.

When there is no translation for a specific message identifier in a locale, the
message identifier itself will be returned by default. Alternately, you can set
a fallback locale which is used to retrieve a fallback translation. To do so,
pass it to the `setFallbackLocale()` method.

## Translating messages

Translating messages is accomplished by calling the `translate()` method of the
translator:

```php
$translator->translate($message, $textDomain, $locale);
```

The message is the message identifier to translate. If it does not exist in the
loader, or is empty, the original message ID will be returned. The text domain
parameter is the one you specified when adding translations. If omitted, the
"default" text domain will be used. The locale parameter will usually not be
used in this context, as by default the locale is taken from the locale set in
the translator.

To translate plural messages, you can use the `translatePlural()` method. It
works similarly to `translate()`, but instead of a single message, it takes a
singular value, a plural value, and an additional integer number on which the
returned plural form is based:

```php
$translator->translatePlural($singular, $plural, $number, $textDomain, $locale);
```

Plural translations are only available if the underlying format supports the
translation of plural messages and plural rule definitions.
