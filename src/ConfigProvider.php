<?php

namespace Laminas\I18n;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Translator\TranslatorInterface;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @final
 */
class ConfigProvider
{
    /**
     * Return general-purpose laminas-i18n configuration.
     *
     * @return array{
     *     dependencies: ServiceManagerConfiguration,
     *     locale: string|null,
     * }
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'locale'       => null,
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return ServiceManagerConfiguration
     */
    public function getDependencyConfig()
    {
        return [
            'aliases'   => [
                'TranslatorPluginManager' => Translator\LoaderPluginManager::class,

                // Legacy Zend Framework aliases
                'Zend\I18n\Translator\TranslatorInterface' => Translator\TranslatorInterface::class,
                'Zend\I18n\Translator\LoaderPluginManager' => Translator\LoaderPluginManager::class,
                Geography\CountryCodeListInterface::class  => Geography\DefaultCountryCodeList::class,
                TranslatorInterface::class                 => Translator\TranslatorInterface::class,
            ],
            'factories' => [
                Translator\TranslatorInterface::class   => Translator\TranslatorServiceFactory::class,
                Translator\LoaderPluginManager::class   => Translator\LoaderPluginManagerFactory::class,
                Geography\DefaultCountryCodeList::class => Geography\DefaultCountryCodeListFactory::class,
            ],
        ];
    }
}
