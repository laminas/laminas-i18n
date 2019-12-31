<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\Translator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LoaderPluginManagerFactory implements FactoryInterface
{
    /**
     * laminas-servicemanager v2 options passed to factory.
     *
     * @param array
     */
    protected $creationOptions = [];

    /**
     * Create and return a LoaderPluginManager.
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param null|array $options
     * @return LoaderPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $options = $options ?: [];
        return new LoaderPluginManager($container, $options);
    }

    /**
     * laminas-servicemanager v2 factory to return LoaderPluginManager
     *
     * @param ServiceLocatorInterface $container
     * @return LoaderPluginManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, 'TranslatorPluginManager', $this->creationOptions);
    }

    /**
     * v2 support for instance creation options.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
