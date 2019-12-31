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
 * @group      Laminas_Validator
 */
class FloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FloatValidator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $locale;

    public function setUp()
    {
        if (! interface_exists('Laminas\Validator\ValidatorInterface')) {
            $this->markTestSkipped(
                'Skipping tests that utilize laminas-validator until that component is '
                . 'forwards-compatible with laminas-stdlib and laminas-servicemanager v3'
            );
        }

        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Float validator under PHP 7; reserved keyword');
        }

        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->locale = Locale::getDefault();
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->locale);
        }
    }

    public function testConstructorRaisesDeprecationNotice()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
        new FloatValidator();
    }
}
