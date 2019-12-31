<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\View;

use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Service manager configuration for i18n view helpers.
 */
class HelperConfig implements ConfigInterface
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $invokables = array(
        'currencyformat'  => 'Laminas\I18n\View\Helper\CurrencyFormat',
        'dateformat'      => 'Laminas\I18n\View\Helper\DateFormat',
        'numberformat'    => 'Laminas\I18n\View\Helper\NumberFormat',
        'plural'          => 'Laminas\I18n\View\Helper\Plural',
        'translate'       => 'Laminas\I18n\View\Helper\Translate',
        'translateplural' => 'Laminas\I18n\View\Helper\TranslatePlural',
    );

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $service) {
            $serviceManager->setInvokableClass($name, $service);
        }
    }
}
