<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\TestAsset;

/**
 * Stub interfact to mock when testing Module::init.
 *
 * Mimics method that will be called on ServiceListener.
 */
interface ServiceListenerInterface
{
    /**
     * @param string $pluginManagerService
     * @param string $configKey
     * @param string $interface
     * @param string $method
     */
    public function addServiceManager(
        $pluginManagerService,
        $configKey,
        $interface,
        $method
    );
}
