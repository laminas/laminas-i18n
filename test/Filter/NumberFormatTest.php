<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Filter;

use Laminas\I18n\Filter\NumberFormat as NumberFormatFilter;
use LaminasTest\I18n\TestCase;
use NumberFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class NumberFormatTest extends TestCase
{
    public function testConstructWithOptions(): void
    {
        $filter = new NumberFormatFilter([
            'locale' => 'en_US',
            'style'  => NumberFormatter::DECIMAL,
        ]);

        self::assertEquals('en_US', $filter->getLocale());
        self::assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    public function testConstructWithParameters(): void
    {
        $filter = new NumberFormatFilter('en_US', NumberFormatter::DECIMAL);

        self::assertEquals('en_US', $filter->getLocale());
        self::assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    /** @return array<array-key, array{0: string, 1: int, 2: NumberFormatter::TYPE_*, 3: float, 4: string}> */
    public static function numberToFormattedProvider(): array
    {
        return [
            [
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1,234,567.891',
            ],
            [
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1.234.567,891',
            ],
            [
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1 234 567,891',
            ],
        ];
    }

    /**
     * @param NumberFormatter::TYPE_* $type
     */
    #[DataProvider('numberToFormattedProvider')]
    public function testNumberToFormatted(string $locale, int $style, int $type, float $value, string $expected): void
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        self::assertEquals($expected, $filter->filter($value));
    }

    /** @return array<array-key, array{0: string, 1: int, 2: NumberFormatter::TYPE_*, 3: string, 4: float}> */
    public static function formattedToNumberProvider(): array
    {
        return [
            [
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1,234,567.891',
                1234567.891,
            ],
            [
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1.234.567,891',
                1234567.891,
            ],
            [
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1 234 567,891',
                1234567.891,
            ],
        ];
    }

    /**
     * @param NumberFormatter::TYPE_* $type
     */
    #[DataProvider('formattedToNumberProvider')]
    public function testFormattedToNumber(string $locale, int $style, int $type, string $value, float $expected): void
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        self::assertEquals($expected, $filter->filter($value));
    }

    /** @return array<array-key, array{0: mixed}> */
    public static function returnUnfilteredDataProvider(): array
    {
        return [
            [null],
            [new stdClass()],
            [
                [
                    '1.234.567,891',
                    '1.567,891',
                ],
            ],
        ];
    }

    /**
     * @param mixed $input
     */
    #[DataProvider('returnUnfilteredDataProvider')]
    public function testReturnUnfiltered($input): void
    {
        $filter = new NumberFormatFilter('de_AT', NumberFormatter::DEFAULT_STYLE, NumberFormatter::TYPE_DOUBLE);

        self::assertEquals($input, $filter->filter($input));
    }
}
