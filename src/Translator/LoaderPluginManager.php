<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\Translator;

use Laminas\I18n\Exception;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for translation loaders.
 *
 * Enforces that loaders retrieved are either instances of
 * Loader\FileLoaderInterface or Loader\RemoteLoaderInterface. Additionally,
 * it registers a number of default loaders.
 *
 * @category   Laminas
 * @package    Laminas_I18n
 * @subpackage Translator
 */
class LoaderPluginManager extends AbstractPluginManager
{
    /**
     * Default set of loaders.
     *
     * @var array
     */
    protected $invokableClasses = array(
        'phparray' => 'Laminas\I18n\Translator\Loader\PhpArray',
        'gettext'  => 'Laminas\I18n\Translator\Loader\Gettext',
    );

    /**
     * Validate the plugin.
     *
     * Checks that the filter loaded is an instance of
     * Loader\FileLoaderInterface or Loader\RemoteLoaderInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Loader\FileLoaderInterface || $plugin instanceof Loader\RemoteLoaderInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Loader\FileLoaderInterface or %s\Loader\RemoteLoaderInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
