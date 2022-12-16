<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use Laminas\Escaper\Escaper;
use Laminas\I18n\Geography\CountryCodeListInterface;
use Laminas\I18n\View\Helper\CountryCodeDataList;
use Locale;
use Psr\Container\ContainerInterface;
use Traversable;

use function assert;
use function is_array;
use function is_string;
use function iterator_to_array;

final class CountryCodeDataListFactory
{
    public function __invoke(ContainerInterface $container): CountryCodeDataList
    {
        /** @psalm-var mixed $config */
        $config = $container->has('config')
            ? $container->get('config')
            : [];

        /** @psalm-var mixed $config */
        $config = $config instanceof Traversable ? iterator_to_array($config) : $config;

        $locale = is_array($config) && isset($config['locale']) && is_string($config['locale'])
            ? $config['locale']
            : Locale::getDefault();

        assert(is_string($locale));

        return new CountryCodeDataList(
            $container->get(CountryCodeListInterface::class),
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
            $locale
        );
    }
}
