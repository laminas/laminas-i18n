<?php

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class FormatterPluginManagerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @psalm-param ServiceManagerConfiguration|null $options
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): FormatterPluginManager {
        $options     ??= [];
        $pluginManager = new FormatterPluginManager($container, $options);

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
        if (! isset($config['translator_formatter']) || ! is_array($config['translator_formatter'])) {
            return $pluginManager;
        }

        // Wire service configuration for translator_plugins
        (new Config($config['translator_formatter']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}
