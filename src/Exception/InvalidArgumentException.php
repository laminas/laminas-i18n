<?php

namespace Laminas\I18n\Exception;

use function sprintf;

/** @final */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
    /** @psalm-pure */
    public static function withInvalidCountryCode(string $received): self
    {
        return new self(sprintf(
            'Country codes should be 2 letter ISO 3166 strings, received "%s"',
            $received
        ));
    }

    /** @psalm-pure */
    public static function withUnknownCountryCode(string $code): self
    {
        return new self(sprintf(
            'The country code "%s" does not correspond to a known country',
            $code
        ));
    }

    /** @psalm-pure */
    public static function withUnrecognizableLocaleString(string $locale): self
    {
        return new self(sprintf(
            'The string "%s" could not be parsed as a valid locale',
            $locale
        ));
    }

    /** @psalm-pure */
    public static function withUndetectableCountryCode(string $localeOrCode): self
    {
        return new self(sprintf(
            'The string "%s" could not be understood as either a locale or an ISO 3166 country code',
            $localeOrCode
        ));
    }
}
