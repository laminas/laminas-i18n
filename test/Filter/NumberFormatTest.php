<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Filter;

use Laminas\I18n\Filter\NumberFormat as NumberFormatFilter;
use NumberFormatter;
use PHPUnit_Framework_TestCase as TestCase;

class NumberFormatTest extends TestCase
{
    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }
    }

    public function testConstructWithOptions()
    {
        $filter = new NumberFormatFilter(array(
            'locale' => 'en_US',
            'style'  => NumberFormatter::DECIMAL
        ));

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    public function testConstructWithParameters()
    {
        $filter = new NumberFormatFilter('en_US', NumberFormatter::DECIMAL);

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }


    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $value
     * @param $expected
     * @dataProvider numberToFormattedProvider
     */
    public function testNumberToFormatted($locale, $style, $type, $value, $expected)
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $value
     * @param $expected
     * @dataProvider formattedToNumberProvider
     */
    public function testFormattedToNumber($locale, $style, $type, $value, $expected)
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        $this->assertEquals($expected, $filter->filter($value));
    }

    public function numberToFormattedProvider()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return array(array());
            }
        }

        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1,234,567.891'
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1.234.567,891'
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1 234 567,891'
            ),
        );
    }

    public function formattedToNumberProvider()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return array(array());
            }
        }

        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1,234,567.891',
                1234567.891,
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1.234.567,891',
                1234567.891,
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1 234 567,891',
                1234567.891,
            ),
        );
    }
}
