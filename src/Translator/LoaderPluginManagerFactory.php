<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function assert;
use function is_array;
use function is_iterable;
use function iterator_to_array;

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
        $options ??= [];
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = ! is_iterable($config) ? [] : $config;
        $config = iterator_to_array($config);

        $plugins = $config['translator_plugins'] ?? [];
        assert(is_array($plugins));

        // Merge arguments to build() over plugins in `config`
        $plugins = array_replace_recursive($plugins, $options);
        /** @psalm-var ServiceManagerConfiguration $plugins */

        return new LoaderPluginManager($container, $plugins);
    }
}
