<?php

declare(strict_types=1);

namespace Laminas\I18n\Geography;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class DefaultCountryCodeListFactory
{
    public function __invoke(): DefaultCountryCodeList
    {
        return DefaultCountryCodeList::create();
    }
}
