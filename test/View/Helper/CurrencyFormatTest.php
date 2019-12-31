<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\CurrencyFormat as CurrencyHelper;
use Locale;

/**
 * @category   Laminas
 * @package    Laminas_View
 * @subpackage UnitTests
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class CurrencyFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyHelper
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new CurrencyHelper();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function currencyTestsDataProvider()
    {
        return array(
            //    locale   currency  number                   expected
            array('de_AT', 'EUR',    1234.56,                 '€ 1.234,56'),
            array('de_AT', 'EUR',    0.123,                   '€ 0,12'),
            array('de_DE', 'EUR',    1234567.891234567890000, '1.234.567,89 €'),
            array('de_DE', 'RUR',    1234567.891234567890000, '1.234.567,89 RUR'),
            array('ru_RU', 'EUR',    1234567.891234567890000, '1 234 567,89 €'),
            array('ru_RU', 'RUR',    1234567.891234567890000, '1 234 567,89 р.'),
            array('en_US', 'EUR',    1234567.891234567890000, '€1,234,567.89'),
            array('en_US', 'RUR',    1234567.891234567890000, 'RUR1,234,567.89'),
            array('en_US', 'USD',    1234567.891234567890000, '$1,234,567.89'),
        );
    }

    /**
     * @dataProvider currencyTestsDataProvider
     */
    public function testBasic($locale, $currencyCode, $number, $expected)
    {
        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $number, $currencyCode, $locale
        ));
    }

    /**
     * @dataProvider currencyTestsDataProvider
     */
    public function testSettersProvideDefaults($locale, $currencyCode, $number, $expected)
    {
        $this->helper
            ->setLocale($locale)
            ->setCurrencyCode($currencyCode);

        $this->assertMbStringEquals($expected, $this->helper->__invoke($number));
    }

    public function testDefaultLocale()
    {
        $this->assertEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function assertMbStringEquals($expected, $test, $message = '')
    {
        $expected = str_replace(array("\xC2\xA0", ' '), '', $expected);
        $test     = str_replace(array("\xC2\xA0", ' '), '', $test);
        $this->assertEquals($expected, $test, $message);
    }
}
