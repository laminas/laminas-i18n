<?php

declare(strict_types=1);

namespace LaminasTest\I18n\TestAsset;

/**
 * Mock interface to use when testing Module::init
 *
 * Mimics Laminas\ModuleManager\ModuleEvent methods called.
 */
interface ModuleEventInterface
{
    /**
     * @param string $name
     * @param mixed $default
     */
    public function getParam($name, $default = null);
}
