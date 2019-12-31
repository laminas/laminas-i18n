<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Float as FloatValidator;
use Locale;

/**
 * @category   Laminas
 * @package    Laminas_Validator
 * @subpackage UnitTests
 * @group      Laminas_Validator
 */
class FloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FloatValidator
     */
    protected $validator;

    /** @var string */
    protected $locale;

    public function setUp()
    {
        $this->locale    = Locale::getDefault();
        $this->validator = new FloatValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
        Locale::setDefault($this->locale);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicProvider
     * @return void
     */
    public function testBasic($value, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($value),
                            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false'));
    }

    public function basicProvider()
    {
        return array(
            array(1.00,   true),
            array(0.01,   true),
            array(-0.1,   true),
            array('10.1', true),
            array('10.0', true),
            array('10.10', true),
            array(1,      true),
            array('10.1not a float', false),
        );
    }
    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(true, $this->validator->isValid('10,5'));
    }

    /**
     * @Laminas-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @Laminas-7489
     */
    public function testUsingApplicationLocale()
    {
        Locale::setDefault('de');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(123,456));
        $this->assertTrue($valid->isValid('123,456'));
    }

    /**
     * @Laminas-7987
     */
    public function testLocaleDeFloatType()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(true, $this->validator->isValid(10.5));
    }

    /**
     * @Laminas-7987
     */
    public function testPhpLocaleDeFloatType()
    {
        Locale::setDefault('de');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(10.5));
    }

    /**
     * @Laminas-7987
     */
    public function testPhpLocaleFrFloatType()
    {
        Locale::setDefault('fr');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(10.5));
    }

    public function deLocaleStringsProvider()
    {
        return array(
            array('1,3',     true),
            array('1000,3',  true),
            array('1.000,3', true),
        );
    }

    /**
     * @Laminas-8919
     * @dataProvider deLocaleStringsProvider
     */
    public function testPhpLocaleDeStringType($float, $expected)
    {
        Locale::setDefault('de_AT');
        $valid = new FloatValidator(array('locale' => 'de_AT'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function frLocaleStringsProvider()
    {
        return array(
            array('1,3',     true),
            array('1000,3',  true),
            array('1 000,3', true),
            array('1.3',     false),
            array('1000.3',  false),
            array('1,000.3', false),
        );
    }

    /**
     * @Laminas-8919
     * @dataProvider frLocaleStringsProvider
     */
    public function testPhpLocaleFrStringType($float, $expected)
    {
        $valid = new FloatValidator(array('locale' => 'fr_FR'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function enLocaleStringsProvider()
    {
        return array(
            array('1.3',     true),
            array('1000.3',  true),
            array('1,000.3', true),
        );
    }

    /**
     * @Laminas-8919
     * @dataProvider enLocaleStringsProvider
     */
    public function testPhpLocaleEnStringType($float, $expected)
    {
        $valid = new FloatValidator(array('locale' => 'en_US'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
