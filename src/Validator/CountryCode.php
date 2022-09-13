<?php

declare(strict_types=1);

namespace Laminas\I18n\Validator;

use Laminas\I18n\CountryCode as Country;
use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\Validator\AbstractValidator;

use function is_string;

final class CountryCode extends AbstractValidator
{
    public const NOT_STRING   = 'notString';
    public const EMPTY_STRING = 'emptyString';
    public const INVALID      = 'invalid';

    /** @var array<string, string> */
    protected $messageTemplates = [
        self::NOT_STRING   => 'Invalid type given. String expected',
        self::EMPTY_STRING => 'Country codes must be non-empty strings',
        self::INVALID      => '"%value%" is not a valid ISO-3166 country code',
    ];

    public function isValid(mixed $value): bool
    {
        if ($value instanceof Country) {
            return true;
        }

        $this->setValue($value);
        if (! is_string($value)) {
            $this->error(self::NOT_STRING);

            return false;
        }

        if ($value === '') {
            $this->error(self::EMPTY_STRING);

            return false;
        }

        try {
            Country::fromString($value);

            return true;
        } catch (InvalidArgumentException) {
            $this->error(self::INVALID);

            return false;
        }
    }
}
