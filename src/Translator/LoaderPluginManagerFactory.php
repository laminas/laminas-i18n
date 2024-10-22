<?php

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function is_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
class LoaderPluginManagerFactory implements FactoryInterface
{
    /**
     * laminas-servicemanager v2 options passed to factory.
     *
     * @deprecated Since 2.16.0 - This component is no longer compatible with Service Manager v2.
     *             This property will be removed in version 3.0
     *
     * @var array
     */
    protected $creationOptions = [];

    /**
     * Create and return a LoaderPluginManager.
     *
     * @param string $name
     * @param array<string, mixed>|null $options
     * @psalm-param ServiceManagerConfiguration|null $options
     * @return LoaderPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, ?array $options = null)
    {
        $options       = $options ?? [];
        $pluginManager = new LoaderPluginManager($container, $options);

        // If this is in a laminas-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        // If we do not have a config service, nothing more to do
        if (! $container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        // If we do not have translator_plugins configuration, nothing more to do
        if (! isset($config['translator_plugins']) || ! is_array($config['translator_plugins'])) {
            return $pluginManager;
        }

        // Wire service configuration for translator_plugins
        $pluginManager->configure($config['translator_plugins']);

        return $pluginManager;
    }

    /**
     * laminas-servicemanager v2 factory to return LoaderPluginManager
     *
     * @deprecated Since 2.16.0 - This component is no longer compatible with Service Manager v2.
     *             This method will be removed in version 3.0
     *
     * @return LoaderPluginManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, 'TranslatorPluginManager', $this->creationOptions);
    }

    /**
     * v2 support for instance creation options.
     *
     * @deprecated Since 2.16.0 - This component is no longer compatible with Service Manager v2.
     *             This method will be removed in version 3.0
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
