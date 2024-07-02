<?php

namespace Laminas\I18n;

use Laminas\ModuleManager\ModuleManager;
use Laminas\ServiceManager\ServiceManager;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
class Module
{
    /**
     * Return laminas-i18n configuration for laminas-mvc application.
     *
     * @return array{
     *     filters: ServiceManagerConfiguration,
     *     service_manager: ServiceManagerConfiguration,
     *     validators: ServiceManagerConfiguration,
     *     view_helpers: ServiceManagerConfiguration,
     * }
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return [
            'filters'         => $provider->getFilterConfig(),
            'service_manager' => $provider->getDependencyConfig(),
            'validators'      => $provider->getValidatorConfig(),
            'view_helpers'    => $provider->getViewHelperConfig(),
        ];
    }

    /**
     * Register a specification for the TranslatorPluginManager with the ServiceListener.
     *
     * @param ModuleManager $moduleManager
     * @return void
     */
    public function init($moduleManager)
    {
        $event           = $moduleManager->getEvent();
        $container       = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'TranslatorPluginManager',
            'translator_plugins',
            'Laminas\ModuleManager\Feature\TranslatorPluginProviderInterface',
            'getTranslatorPluginConfig'
        );
    }
}
