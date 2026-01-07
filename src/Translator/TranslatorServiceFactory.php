<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class TranslatorServiceFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): Translator {
        // Configure the translator
        $config   = $container->get('config');
        $trConfig = $config['translator'] ?? [];

        return Translator::factory($container->get(MessageLoaderPluginManagerInterface::class), $trConfig);
    }
}
