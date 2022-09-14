<?php

namespace Laminas\I18n;

use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * @see ConfigInterface
 *
 * @psalm-import-type ServiceManagerConfigurationType from ConfigInterface
 * @final
 */
class ConfigProvider
{
    /**
     * Return general-purpose laminas-i18n configuration.
     *
     * @return array{
     *     dependencies: ServiceManagerConfigurationType,
     *     filters: ServiceManagerConfigurationType,
     *     validators: ServiceManagerConfigurationType,
     *     view_helpers: ServiceManagerConfigurationType,
     * }
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'filters'      => $this->getFilterConfig(),
            'validators'   => $this->getValidatorConfig(),
            'view_helpers' => $this->getViewHelperConfig(),
            'locale'       => null,
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return ServiceManagerConfigurationType
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
            ],
            'factories' => [
                Translator\TranslatorInterface::class   => Translator\TranslatorServiceFactory::class,
                Translator\LoaderPluginManager::class   => Translator\LoaderPluginManagerFactory::class,
                Geography\DefaultCountryCodeList::class => [Geography\DefaultCountryCodeList::class, 'create'],
            ],
        ];
    }

    /**
     * Return laminas-filter configuration.
     *
     * @return ServiceManagerConfigurationType
     */
    public function getFilterConfig()
    {
        return [
            'aliases'   => [
                'alnum'        => Filter\Alnum::class,
                'Alnum'        => Filter\Alnum::class,
                'alpha'        => Filter\Alpha::class,
                'Alpha'        => Filter\Alpha::class,
                'numberformat' => Filter\NumberFormat::class,
                'numberFormat' => Filter\NumberFormat::class,
                'NumberFormat' => Filter\NumberFormat::class,
                'numberparse'  => Filter\NumberParse::class,
                'numberParse'  => Filter\NumberParse::class,
                'NumberParse'  => Filter\NumberParse::class,

                // Legacy Zend Framework aliases
                'Zend\I18n\Filter\Alnum'        => Filter\Alnum::class,
                'Zend\I18n\Filter\Alpha'        => Filter\Alpha::class,
                'Zend\I18n\Filter\NumberFormat' => Filter\NumberFormat::class,
                'Zend\I18n\Filter\NumberParse'  => Filter\NumberParse::class,
            ],
            'factories' => [
                Filter\Alnum::class        => InvokableFactory::class,
                Filter\Alpha::class        => InvokableFactory::class,
                Filter\NumberFormat::class => InvokableFactory::class,
                Filter\NumberParse::class  => InvokableFactory::class,
            ],
        ];
    }

    /**
     * Return laminas-validator configuration.
     *
     * @return ServiceManagerConfigurationType
     */
    public function getValidatorConfig()
    {
        return [
            'aliases'   => [
                'alnum'       => Validator\Alnum::class,
                'Alnum'       => Validator\Alnum::class,
                'alpha'       => Validator\Alpha::class,
                'Alpha'       => Validator\Alpha::class,
                'datetime'    => Validator\DateTime::class,
                'dateTime'    => Validator\DateTime::class,
                'DateTime'    => Validator\DateTime::class,
                'float'       => Validator\IsFloat::class,
                'Float'       => Validator\IsFloat::class,
                'int'         => Validator\IsInt::class,
                'Int'         => Validator\IsInt::class,
                'isfloat'     => Validator\IsFloat::class,
                'isFloat'     => Validator\IsFloat::class,
                'IsFloat'     => Validator\IsFloat::class,
                'isint'       => Validator\IsInt::class,
                'isInt'       => Validator\IsInt::class,
                'IsInt'       => Validator\IsInt::class,
                'phonenumber' => Validator\PhoneNumber::class,
                'phoneNumber' => Validator\PhoneNumber::class,
                'PhoneNumber' => Validator\PhoneNumber::class,
                'postcode'    => Validator\PostCode::class,
                'postCode'    => Validator\PostCode::class,
                'PostCode'    => Validator\PostCode::class,

                // Legacy Zend Framework aliases
                'Zend\I18n\Validator\Alnum'       => Validator\Alnum::class,
                'Zend\I18n\Validator\Alpha'       => Validator\Alpha::class,
                'Zend\I18n\Validator\DateTime'    => Validator\DateTime::class,
                'Zend\I18n\Validator\IsFloat'     => Validator\IsFloat::class,
                'Zend\I18n\Validator\IsInt'       => Validator\IsInt::class,
                'Zend\I18n\Validator\PhoneNumber' => Validator\PhoneNumber::class,
                'Zend\I18n\Validator\PostCode'    => Validator\PostCode::class,
            ],
            'factories' => [
                Validator\Alnum::class       => InvokableFactory::class,
                Validator\Alpha::class       => InvokableFactory::class,
                Validator\DateTime::class    => InvokableFactory::class,
                Validator\IsFloat::class     => InvokableFactory::class,
                Validator\IsInt::class       => InvokableFactory::class,
                Validator\PhoneNumber::class => InvokableFactory::class,
                Validator\PostCode::class    => InvokableFactory::class,
            ],
        ];
    }

    /**
     * Return laminas-view helper configuration.
     *
     * Obsoletes View\HelperConfig.
     *
     * @return ServiceManagerConfigurationType
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
