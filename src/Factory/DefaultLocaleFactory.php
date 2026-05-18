<?php

declare(strict_types=1);

namespace Laminas\I18n\Factory;

use Laminas\I18n\DefaultLocale;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Locale;
use Psr\Container\ContainerInterface;

use function assert;
use function is_iterable;
use function is_string;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class DefaultLocaleFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): DefaultLocale {
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        /** @psalm-var mixed $locale */
        $locale = $config['locale'] ?? null;
        $locale = is_string($locale) && $locale !== '' ? $locale : Locale::getDefault();
        assert($locale !== '');

        return new DefaultLocale($locale);
    }
}
