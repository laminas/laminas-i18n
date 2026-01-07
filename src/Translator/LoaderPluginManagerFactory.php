<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
final readonly class LoaderPluginManagerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): LoaderPluginManager {
        $options     ??= [];
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
}
