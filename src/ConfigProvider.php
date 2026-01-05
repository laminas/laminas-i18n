<?php

declare(strict_types=1);

namespace Laminas\I18n;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Translator\TranslatorInterface;

/**
 * phpcs:disable Generic.Files.LineLength
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
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
     *     },
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
            'aliases'   => [
                'TranslatorPluginManager'                             => Translator\LoaderPluginManager::class,
                Translator\MessageLoaderPluginManagerInterface::class => Translator\LoaderPluginManager::class,
                Geography\CountryCodeListInterface::class             => Geography\DefaultCountryCodeList::class,
                TranslatorInterface::class                            => Translator\Translator::class,
            ],
            'factories' => [
                Translator\Loader\IniFileReader::class  => Translator\Loader\Factory\IniFileReaderFactory::class,
                Translator\Translator::class            => Translator\TranslatorServiceFactory::class,
                Translator\LoaderPluginManager::class   => Translator\LoaderPluginManagerFactory::class,
                Geography\DefaultCountryCodeList::class => Geography\DefaultCountryCodeListFactory::class,
                DefaultLocale::class                    => Factory\DefaultLocaleFactory::class,
                I18nDefaults::class                     => Factory\I18nDefaultsFactory::class,
            ],
        ];
    }
}
