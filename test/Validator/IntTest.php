<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Int as IntValidator;
use Locale;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Validator
 */
class IntTest extends TestCase
{
    /**
     * @var Int
     */
    protected $validator;

    /**
     * @var string
     */
    protected $locale;

    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Int validator under PHP 7; reserved keyword');
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
        new IntValidator();
    }
}
