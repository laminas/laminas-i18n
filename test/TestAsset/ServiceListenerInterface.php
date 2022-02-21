<?php

declare(strict_types=1);

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
