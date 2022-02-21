<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\IsFloat as IsFloatValidator;
use LaminasTest\I18n\TestCase;
use Locale;
use NumberFormatter;

use function sprintf;

use const INTL_ICU_DATA_VERSION;
use const INTL_ICU_VERSION;

class IsFloatTest extends TestCase
{
    private IsFloatValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new IsFloatValidator(['locale' => 'en']);
    }

    /**
     * Test float and integer type variables. Includes decimal and scientific notation NumberFormatter-formatted
     * versions. Should return true for all locales.
     *
     * @param mixed   $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider floatAndIntegerProvider
     */
    public function testFloatAndIntegers($value, bool $expected, string $locale, string $type): void
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false')
            . sprintf(' (locale:%s, type:%s)', $locale, $type) . ', ICU Version:' . INTL_ICU_VERSION . '-'
            . INTL_ICU_DATA_VERSION
        );
    }

    /** @return array<array-key, array{0: mixed, 1: bool, 2: string, 3: string}> */
    public function floatAndIntegerProvider(): array
    {
        $trueArray       = [];
        $testingLocales  = ['ar', 'bn', 'de', 'dz', 'en', 'fr-CH', 'ja', 'ks', 'ml-IN', 'mr', 'my', 'ps', 'ru'];
        $testingExamples = [
            1000,
            -2000,
            +398.00,
            0.04,
            -0.5,
            .6,
            -.70,
            8E10,
            -9.3456E-2,
            10.23E6,
            123.1234567890987654321,
            1,
            13,
            -3,
        ];

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingLocales as $locale) {
            foreach ($testingExamples as $example) {
                $trueArray[] = [$example, true, $locale, 'raw'];
                //Decimal Formatted
                $trueArray[] = [
                    NumberFormatter::create($locale, NumberFormatter::DECIMAL)
                        ->format($example, NumberFormatter::TYPE_DOUBLE),
                    true,
                    $locale,
                    'decimal',
                ];
                //Scientific Notation Formatted
                $trueArray[] = [
                    NumberFormatter::create($locale, NumberFormatter::SCIENTIFIC)
                        ->format($example, NumberFormatter::TYPE_DOUBLE),
                    true,
                    $locale,
                    'scientific',
                ];
            }
        }
        return $trueArray;
    }

    /**
     * Test manually-generated strings for specific locales. These are "look-alike" strings where graphemes such as
     * NO-BREAK SPACE, ARABIC THOUSANDS SEPARATOR, and ARABIC DECIMAL SEPARATOR are replaced with more typical ASCII
     * characters.
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider lookAlikeProvider
     */
    public function testlookAlikes(string $value, bool $expected, string $locale): void
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') . sprintf(' (locale:%s)', $locale)
        );
    }

    /** @return array<array-key, array{0: string, 1: bool, 2: string}> */
    public function lookAlikeProvider(): array
    {
        $trueArray    = [];
        $testingArray = [
            'ar' => "\xD9\xA1'\xD9\xA1\xD9\xA1\xD9\xA1,\xD9\xA2\xD9\xA3",
            'ru' => '2 000,00',
        ];

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingArray as $locale => $example) {
            $trueArray[] = [$example, true, $locale];
        }
        return $trueArray;
    }

    /**
     * Test manually-generated strings for specific locales. These are "look-alike" strings where graphemes such as
     * NO-BREAK SPACE, ARABIC THOUSANDS SEPARATOR, and ARABIC DECIMAL SEPARATOR are replaced with more typical ASCII
     * characters.
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider validationFailureProvider
     */
    public function testValidationFailures(string $value, bool $expected, string $locale): void
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') . sprintf(' (locale:%s)', $locale)
        );
    }

    /** @return array<array-key, array{0: string, 1: bool, 2: string}> */
    public function validationFailureProvider(): array
    {
        $trueArray    = [];
        $testingArray = [
            'ar'    => ['10.1', '66notflot.6'],
            'ru'    => ['10.1', '66notflot.6', '2,000.00', '2 00'],
            'en'    => ['10,1', '66notflot.6', '2.000,00', '2 000', '2,00'],
            'fr-CH' => ['66notflot.6', '2,000.00', "2'00"],
        ];

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingArray as $locale => $exampleArray) {
            foreach ($exampleArray as $example) {
                $trueArray[] = [$example, false, $locale];
            }
        }
        return $trueArray;
    }

    /**
     * Ensures that getMessages() returns expected default value
     */
    public function testGetMessages(): void
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales(): void
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
    }

    public function testNonStringValidation(): void
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testUsingApplicationLocale(): void
    {
        Locale::setDefault('de');
        $valid = new IsFloatValidator();
        $this->assertEquals('de', $valid->getLocale());
    }

    public function testEqualsMessageTemplates(): void
    {
        $validator = $this->validator;

        $this->assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }

    public function testNotFloat(): void
    {
        $this->assertFalse($this->validator->isValid('2.000.000,00'));

        $message = $this->validator->getMessages();
        $this->assertStringContainsString('does not appear to be a float', $message['notFloat']);
    }
}
