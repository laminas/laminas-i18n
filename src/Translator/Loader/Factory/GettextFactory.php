<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader\Factory;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function is_array;
use function is_bool;
use function is_iterable;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class GettextFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): Gettext {
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        /** @psalm-var mixed $i18n */
        $i18n = $config['laminas-i18n'] ?? [];
        $i18n = is_array($i18n) ? $i18n : [];

        /** @psalm-var mixed $configuredOptions */
        $configuredOptions = $i18n['gettext-loader-options'] ?? [];
        $configuredOptions = is_array($configuredOptions) ? $configuredOptions : [];

        $useIncludePath = $configuredOptions['use-include-path'] ?? false;
        if (! is_bool($useIncludePath)) {
            throw new RuntimeException('Gettext include path option must be boolean');
        }

        return new Gettext($useIncludePath);
    }
}
