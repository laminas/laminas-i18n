<?php

declare(strict_types=1);

namespace Laminas\I18n;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Translator\TranslatorInterface;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
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
     *     }
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'locale'       => null,
            'timezone'     => null,
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
