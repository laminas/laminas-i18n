<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\NumberFormat as NumberFormatHelper;
use LaminasTest\I18n\TestCase;
use Locale;
use NumberFormatter;

use function str_replace;

class NumberFormatTest extends TestCase
{
    private NumberFormatHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new NumberFormatHelper();
    }

    /** @return array<array-key, array{0: string, 1: int, 2: int, 3: int|null, 4: array<int, string>, 5: float, 6: string}> */
    public function currencyTestsDataProvider(): array
    {
        return [
            [
                'de_DE',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1.234.567,891',
            ],
            [
                'de_DE',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                6,
                [],
                1234567.891234567890000,
                '1.234.567,891235',
            ],
            [
                'de_DE',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123.456.789 %',
            ],
            [
                'de_DE',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                1,
                [],
                1234567.891234567890000,
                '123.456.789,1 %',
            ],
            [
                'de_DE',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1,23456789123456E6',
            ],
            [
                'ru_RU',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1 234 567,891',
            ],
            [
                'ru_RU',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123 456 789 %',
            ],
            [
                'ru_RU',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1,23456789123456E6',
            ],
            [
                'en_US',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1,234,567.891',
            ],
            [
                'en_US',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123,456,789%',
            ],
            [
                'en_US',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1.23456789123456E6',
            ],
            [
                'en_US',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [
                    NumberFormatter::NEGATIVE_PREFIX => 'MINUS',
                ],
                -1234567.891234567890000,
                'MINUS123,456,789%',
            ],
        ];
    }

    /**
     * @dataProvider currencyTestsDataProvider
     * @param array<int, string> $textAttributes
     */
    public function testBasic(
        string $locale,
        int $formatStyle,
        int $formatType,
        ?int $decimals,
        array $textAttributes,
        float $number,
        string $expected
    ) {
        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $number,
            $formatStyle,
            $formatType,
            $locale,
            $decimals,
            $textAttributes
        ));
    }

    /**
     * @dataProvider currencyTestsDataProvider
     * @param array<int, string> $textAttributes
     */
    public function testSettersProvideDefaults(
        string $locale,
        int $formatStyle,
        int $formatType,
        ?int $decimals,
        array $textAttributes,
        float $number,
        string $expected
    ): void {
        $this->helper
             ->setLocale($locale)
             ->setFormatStyle($formatStyle)
             ->setDecimals($decimals)
             ->setFormatType($formatType)
             ->setTextAttributes($textAttributes);

        $this->assertMbStringEquals($expected, $this->helper->__invoke($number));
    }

    public function testDefaultLocale(): void
    {
        $this->assertEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function assertMbStringEquals(string $expected, string $test, string $message = ''): void
    {
        $expected = str_replace(["\xC2\xA0", ' '], '', $expected);
        $test     = str_replace(["\xC2\xA0", ' '], '', $test);
        $this->assertEquals($expected, $test, $message);
    }
}
