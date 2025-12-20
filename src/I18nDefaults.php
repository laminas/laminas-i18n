<?php

declare(strict_types=1);

namespace Laminas\I18n;

use DateTimeZone;

final readonly class I18nDefaults
{
    /**
     * @param non-empty-string $defaultCurrencyCode
     * @param non-empty-string $defaultTextDomain
     * @param non-empty-string $defaultLocale
     */
    public function __construct(
        public DateTimeZone $defaultTimeZone,
        public string $defaultCurrencyCode,
        public string $defaultTextDomain,
        public string $defaultLocale,
        public CountryCode $defaultCountry,
    ) {
    }
}
