<?php

namespace Laminas\I18n;

use Laminas\ServiceManager\Factory\InvokableFactory;
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
     *     view_helpers: ServiceManagerConfiguration,
     *     locale: string|null,
     * }
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'view_helpers' => $this->getViewHelperConfig(),
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

    /**
     * Return laminas-view helper configuration.
     *
     * Obsoletes View\HelperConfig.
     *
     * @return ServiceManagerConfiguration
     */
    public function getViewHelperConfig()
    {
        return [
            'aliases'   => [
                'countryCodeDataList' => View\Helper\CountryCodeDataList::class,
                'currencyformat'      => View\Helper\CurrencyFormat::class,
                'currencyFormat'      => View\Helper\CurrencyFormat::class,
                'CurrencyFormat'      => View\Helper\CurrencyFormat::class,
                'dateformat'          => View\Helper\DateFormat::class,
                'dateFormat'          => View\Helper\DateFormat::class,
                'DateFormat'          => View\Helper\DateFormat::class,
                'numberformat'        => View\Helper\NumberFormat::class,
                'numberFormat'        => View\Helper\NumberFormat::class,
                'NumberFormat'        => View\Helper\NumberFormat::class,
                'plural'              => View\Helper\Plural::class,
                'Plural'              => View\Helper\Plural::class,
                'translate'           => View\Helper\Translate::class,
                'Translate'           => View\Helper\Translate::class,
                'translateplural'     => View\Helper\TranslatePlural::class,
                'translatePlural'     => View\Helper\TranslatePlural::class,
                'TranslatePlural'     => View\Helper\TranslatePlural::class,

                // Legacy Zend Framework aliases
                'Zend\I18n\View\Helper\CurrencyFormat'  => View\Helper\CurrencyFormat::class,
                'Zend\I18n\View\Helper\DateFormat'      => View\Helper\DateFormat::class,
                'Zend\I18n\View\Helper\NumberFormat'    => View\Helper\NumberFormat::class,
                'Zend\I18n\View\Helper\Plural'          => View\Helper\Plural::class,
                'Zend\I18n\View\Helper\Translate'       => View\Helper\Translate::class,
                'Zend\I18n\View\Helper\TranslatePlural' => View\Helper\TranslatePlural::class,
            ],
            'factories' => [
                View\Helper\CountryCodeDataList::class => View\Helper\Container\CountryCodeDataListFactory::class,
                View\Helper\CurrencyFormat::class      => InvokableFactory::class,
                View\Helper\DateFormat::class          => InvokableFactory::class,
                View\Helper\NumberFormat::class        => InvokableFactory::class,
                View\Helper\Plural::class              => InvokableFactory::class,
                View\Helper\Translate::class           => InvokableFactory::class,
                View\Helper\TranslatePlural::class     => InvokableFactory::class,
            ],
        ];
    }
}
