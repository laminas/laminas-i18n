<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\CurrencyFormat as CurrencyFormatHelper;
use LaminasTest\I18n\TestCase;
use Locale;

use function str_replace;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class CurrencyFormatTest extends TestCase
{
    /**
     * @var CurrencyFormatHelper
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new CurrencyFormatHelper();
    }

    public function currencyProvider()
    {
        return [
            //    locale   currency     show decimals       number      currencyPattern             expected
            ['de_DE', 'EUR',       true, 1234567.891234567890000,  null,                       '1.234.567,89 €'],
            ['de_DE', 'RUR',       true, 1234567.891234567890000,  null,                       '1.234.567,89 RUR'],
            ['ru_RU', 'EUR',       true, 1234567.891234567890000,  null,                       '1 234 567,89 €'],
            ['ru_RU', 'RUR',       true, 1234567.891234567890000,  null,                       '1 234 567,89 р.'],
            ['en_US', 'EUR',       true, 1234567.891234567890000,  null,                       '€1,234,567.89'],
            ['en_US', 'RUR',       true, 1234567.891234567890000,  null,                       'RUR1,234,567.89'],
            ['en_US', 'USD',       true, 1234567.891234567890000,  null,                       '$1,234,567.89'],
            ['de_DE', 'EUR',       false, 1234567.891234567890000, null,                       '1.234.568 €'],
            ['de_DE', 'RUB',       false, 1234567.891234567890000, null,                       '1.234.568 RUB'],
            //array('ru_RU', 'EUR',     false,             1234567.891234567890000,  null, '1 234 568 €'),
            //array('ru_RU', 'RUR',     false,             1234567.891234567890000,  null, '1 234 567 р.'),
            //array('en_US', 'EUR',     false,             1234567.891234567890000,  null, '€1,234,568'),
            //array('en_US', 'EUR',     false,             1234567.891234567890000,  null, '€1,234,568'),
            ['en_US', 'USD',       false, 1234567.891234567890000, null,                       '$1,234,568'],
            /* @see http://bugs.icu-project.org/trac/ticket/10997 */
            ['en_US', 'EUR',       false, 1234567.891234567890000, null,                       '€1,234,568'],
            ['de_DE', 'USD',       false, 1234567.891234567890000, null,                       '1.234.568 $'],
            ['en_US', 'PLN',       false, 1234567.891234567890000, null,                       'PLN 1,234,568'],
            ['de_DE', 'PLN',       false, 1234567.891234567890000, null,                       '1.234.568 PLN'],
        ];
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testBasic($locale, $currencyCode, $showDecimals, $number, $currencyPattern, $expected)
    {
        $this->assertMbStringEquals(
            $expected,
            $this->helper->__invoke(
                $number,
                $currencyCode,
                $showDecimals,
                $locale,
                $currencyPattern
            )
        );
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testSettersProvideDefaults(
        $locale,
        $currencyCode,
        $showDecimals,
        $number,
        $currencyPattern,
        $expected
    ) {
        $this->helper
            ->setLocale($locale)
            ->setShouldShowDecimals($showDecimals)
            ->setCurrencyCode($currencyCode)
            ->setCurrencyPattern($currencyPattern);

        $this->assertMbStringEquals($expected, $this->helper->__invoke($number));
    }

    public function testViewhelperExecutedSequentially()
    {
        $helper = $this->helper;
        $helper->setShouldShowDecimals(true);

        $this->assertEquals('1.234,43 €', $helper(1234.4321, 'EUR', null, 'de_DE'));
        $this->assertEquals('1.234 €', $helper(1234.4321, 'EUR', false, 'de_DE'));
        $this->assertEquals('1.234,43 €', $helper(1234.4321, 'EUR', null, 'de_DE'));
    }

    public function testDefaultLocale()
    {
        $this->assertMbStringEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function assertMbStringEquals($expected, $test, $message = '')
    {
        $expected = str_replace(["\xC2\xA0", ' '], '', $expected);
        $test = str_replace(["\xC2\xA0", ' '], '', $test);
        $this->assertEquals($expected, $test, $message);
    }
}
