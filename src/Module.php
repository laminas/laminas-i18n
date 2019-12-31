<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n;

class Module
{
    /**
     * Return laminas-i18n configuration for laminas-mvc application.
     *
     * @return array
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
}
