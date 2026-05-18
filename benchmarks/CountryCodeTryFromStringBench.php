<?php

declare(strict_types=1);

namespace LaminasBench\I18n;

use Laminas\I18n\CountryCode;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

#[Revs(100)]
#[Iterations(20)]
#[Warmup(2)]
final readonly class CountryCodeTryFromStringBench
{
    private const INVALID = [
        'Empty String'         => '',
        'Invalid Country Code' => 'ZZ',
        'Invalid String'       => 'wrong',
        'Numeric String'       => '123',
        'Invalid Locale'       => 'zz_ZZ',
    ];

    private const VALID = [
        'GB',
        'UA',
        'us',
        'sl-Latn-IT-nedis',
        'de_DE',
    ];

    public function benchInvalidArgumentToTryFromString(): void
    {
        foreach (self::INVALID as $input) {
            /** @psalm-suppress PossiblyInvalidArgument */
            CountryCode::tryFromString($input);
        }
    }

    public function benchValidInputFromString(): void
    {
        foreach (self::VALID as $input) {
            CountryCode::tryFromString($input);
        }
    }
}
