<?php

declare(strict_types=1);

namespace Laminas\I18n\Geography;

final class DefaultCountryCodeListFactory
{
    public function __invoke(): DefaultCountryCodeList
    {
        return DefaultCountryCodeList::create();
    }
}
