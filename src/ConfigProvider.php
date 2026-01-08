<?php

declare(strict_types=1);

namespace Laminas\I18n;

use Laminas\I18n\Translator\TranslationCollector\Factory\PSR6CachingCollectorDelegatorFactory;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\I18n\Translator\Value\TranslationFile;
use Laminas\I18n\Translator\Value\TranslatorFilePattern;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Translator\TranslatorInterface;

/**
 * phpcs:disable Generic.Files.LineLength
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @psalm-import-type TranslationFileSpec from TranslationFile
 * @psalm-import-type TranslationFilePatternSpec from TranslatorFilePattern
 */
final class ConfigProvider
{
    /**
     * Return general-purpose laminas-i18n configuration.
     *
     * @return array{
     *     dependencies: ServiceManagerConfiguration,
     *     locale: non-empty-string|null,
     *     timezone: non-empty-string|null,
     *     laminas-i18n?: array{
     *         defaultTimeZone?: non-empty-string|null,
     *         defaultCurrency?: non-empty-string|null,
     *         defaultTextDomain?: non-empty-string|null,
     *         defaultCountry?: non-empty-string|null,
     *         ini-format-options?: array{
     *             process-sections?: bool,
     *             typed?: bool,
     *             nest-separator?: string,
     *         },
     *         gettext-loader-options?: array{
     *             use-include-path?: bool,
     *         },
     *         php-loader-options?: array{
     *             use-include-path?: bool,
     *         },
     *         translator?: array{
     *             translation_files?: array<non-empty-string, array<non-empty-string, TranslationFileSpec>>,
     *             translation_file_patterns?: list<TranslationFilePatternSpec>,
     *             remote_translation?: list<array{type: non-empty-string, text_domain?: non-empty-string}>,
     *             aggregate_collector?: list<class-string<TranslationCollectorInterface>>,
     *             cache?: string|null,
     *             event_manager_enabled?: bool,
     *             fallback_locale?: string|null,
     *             psr6_cache?: string,
     *             cache_key_prefix?: non-empty-string|null,
     *         },
     *     },
     *     translator_plugins?: ServiceManagerConfiguration,
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            /**
             * The default locale can be configured here.
             *
             * When null (The default), the default locale will be whatever PHP's built-in Locale::getDefault() returns
             */
            'locale' => null,
            /**
             * Setting the timezone at the top-level is deprecated because it could conflict with other libraries.
             *
             * Please set the default time zone under `laminas-i18n.defaultTimeZone`
             *
             * @deprecated
             */
            'timezone'     => null,
            'laminas-i18n' => [
                /**
                 * The default timezone will be retrieved from date_default_timezone_get() when this value is null.
                 * It is not used directly in this library, but is used by other Laminas-i18n related libraries and is
                 * passed to the {@link I18nDefaults} class.
                 */
                // 'defaultTimeZone' => 'Africa/Nairobi',
                /**
                 * The default currency code is not used directly in this library, but other satellite packages can
                 * depend on it. When not set, the currency is extrapolated from the default locale.
                 * The value is passed to the {@link I18nDefaults} class.
                 */
                // 'defaultCurrency' => 'NZD',
                /**
                 * The default text domain used when determining translations. This value falls back to a global default
                 * value of 'default' when not set (The default)
                 */
                // 'defaultTextDomain' => 'something-else',
                /**
                 * The default country is used for determining a default geographic location when one cannot be extrapolated
                 * from other inputs. By default, the country will be detected from whatever the default locale is, for
                 * example, the country will be 'CA' when the default locale is set to 'fr_CA'.
                 */
                // 'defaultCountry' => 'UA',

                /** Default options for the {@link Translator\Loader\IniFileReader} */
                'ini-format-options' => [
                    'process-sections' => true,
                    'typed'            => false,
                    'nest-separator'   => '.',
                ],
                /** Default options for the {@link Translator\Loader\Gettext} translation file loader */
                'gettext-loader-options' => [
                    'use-include-path' => false,
                ],
                /** Default options for the {@link Translator\Loader\PhpArray} translation file loader */
                'php-loader-options' => [
                    'use-include-path' => false,
                ],

                /**
                 * Configuration for the translator and translation file collectors
                 */
                'translator' => [
                    /**
                     * File list configuration for the {@link Translator\TranslationCollector\FileListCollector}
                     *
                     * The expected array format is:
                     * 'translation_files' => [
                     *     [
                     *         'type' => 'class name or alias of a FileLoaderInterface',
                     *         'filename' => 'The file name/path to load',
                     *         'locale' => 'Optional locale, falling back to the default locale',
                     *         'text_domain' => 'Optional text domain, falling back to the default text domain',
                     *     ],
                     *     // more entries…
                     * ],
                     */
                    'translation_files' => [],

                    /**
                     * List of file patterns for the {@link Translator\TranslationCollector\FilePatternCollector}
                     *
                     * The expected array format is:
                     * 'translation_file_patterns' => [
                     *      [
                     *          'type' => 'class name or alias of a FileLoaderInterface',
                     *          'base_dir' => 'The directory to search for files',
                     *          'pattern' => 'The file name pattern in printf format',
                     *          'text_domain' => 'Optional text domain, falling back to the default text domain',
                     *      ],
                     *      // more entries…
                     *  ],
                     */
                    'translation_file_patterns' => [],

                    /**
                     * Remote translation configuration for the {@link Translator\TranslationCollector\RemoteListCollector}
                     *
                     * The expected array format is:
                     * 'remote_translation' => [
                     *     [
                     *         'type' => 'class name or alias of a RemoteLoaderInterface',
                     *         'text_domain' => 'Optional text domain, falling back to the default text domain',
                     *     ],
                     * ],
                     */
                    'remote_translation' => [],

                    /**
                     * Aggregate Collector configuration
                     *
                     * The default collector is an AggregateCollector that composes an instance of each other collector type.
                     * You can override the defaults by configuring a list of collector class names
                     */
                    //'aggregate_collector' => [CollectorOne::class, CollectorTwo::class],

                    /**
                     * Whether to enable the event manager in the translator
                     */
                    'event_manager_enabled' => false,

                    /**
                     * Optionally provide a fallback locale for when the translators default locale has no translations
                     */
                    'fallback_locale' => null,

                    /**
                     * When you provide a service name here that points to a PSR-16 cache item pool that is
                     * retrievable from the DI container, the default translation collector will be wrapped in
                     * a {@link Translator\TranslationCollector\PSR6CachingCollector}
                     */
                    // 'psr6_cache' => 'Some Cache Service ID',

                    /**
                     * You can customise the cache-key prefix if you want.
                     * By default, it is 'LaminasTranslations'
                     */
                    'cache_key_prefix' => null,
                ],
            ],
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return ServiceManagerConfiguration
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases'    => [
                'TranslatorPluginManager'                             => Translator\LoaderPluginManager::class,
                Translator\MessageLoaderPluginManagerInterface::class => Translator\LoaderPluginManager::class,
                TranslationCollectorInterface::class                  => Translator\TranslationCollector\AggregateCollector::class,
                Geography\CountryCodeListInterface::class             => Geography\DefaultCountryCodeList::class,
                TranslatorInterface::class                            => Translator\Translator::class,
            ],
            'factories'  => [
                Translator\TranslationCollector\AggregateCollector::class   => Translator\TranslationCollector\Factory\AggregateCollectorFactory::class,
                Translator\TranslationCollector\FileListCollector::class    => Translator\TranslationCollector\Factory\FileListCollectorFactory::class,
                Translator\TranslationCollector\FilePatternCollector::class => Translator\TranslationCollector\Factory\FilePatternCollectorFactory::class,
                Translator\TranslationCollector\RemoteListCollector::class  => Translator\TranslationCollector\Factory\RemoteListCollectorFactory::class,
                Translator\Loader\IniFileReader::class                      => Translator\Loader\Factory\IniFileReaderFactory::class,
                Translator\Translator::class                                => Translator\TranslatorServiceFactory::class,
                Translator\LoaderPluginManager::class                       => Translator\LoaderPluginManagerFactory::class,
                Geography\DefaultCountryCodeList::class                     => Geography\DefaultCountryCodeListFactory::class,
                DefaultLocale::class                                        => Factory\DefaultLocaleFactory::class,
                I18nDefaults::class                                         => Factory\I18nDefaultsFactory::class,
            ],
            'delegators' => [
                Translator\TranslationCollector\AggregateCollector::class => [
                    PSR6CachingCollectorDelegatorFactory::class => PSR6CachingCollectorDelegatorFactory::class,
                ],
            ],
        ];
    }
}
