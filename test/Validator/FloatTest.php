<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Float as FloatValidator;
use Locale;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Validator
 */
class FloatTest extends TestCase
{
    /**
     * @var FloatValidator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $locale;

    protected function setUp(): void
    {
        if (PHP_VERSION_ID >= 70000) {
            $this->markTestSkipped('Cannot test Float validator under PHP 7; reserved keyword');
        }

        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->locale = Locale::getDefault();
    }

    protected function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->locale);
        }
    }

    public function testConstructorRaisesDeprecationNotice()
    {
        $this->expectException('PHPUnit_Framework_Error_Deprecated');
        new FloatValidator();
    }
}
