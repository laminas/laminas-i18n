<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader\Factory;

use Laminas\I18n\Translator\Loader\Ini;
use Laminas\I18n\Translator\Loader\IniFileReader;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class IniFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): Ini {
        return new Ini($container->get(IniFileReader::class));
    }
}
