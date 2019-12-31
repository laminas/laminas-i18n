<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\Translate as TranslateHelper;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class TranslateTest extends TestCase
{
    /**
     * @var TranslateHelper
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
        $this->helper = new TranslateHelper();
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

    public function testInvokingWithoutTranslatorWillRaiseException()
    {
        $this->expectException('Laminas\I18n\Exception\RuntimeException');
        $this->helper->__invoke('message');
    }

    public function testDefaultInvokeArguments()
    {
        $input    = 'input';
        $expected = 'translated';

        $translatorMock = $this->createMock('Laminas\I18n\Translator\Translator');
        $translatorMock->expects($this->once())
                       ->method('translate')
                       ->with($this->equalTo($input), $this->equalTo('default'), $this->equalTo(null))
                       ->will($this->returnValue($expected));

        $this->helper->setTranslator($translatorMock);

        $this->assertEquals($expected, $this->helper->__invoke($input));
    }

    public function testCustomInvokeArguments()
    {
        $input      = 'input';
        $expected   = 'translated';
        $textDomain = 'textDomain';
        $locale     = 'en_US';

        $translatorMock = $this->createMock('Laminas\I18n\Translator\Translator');
        $translatorMock->expects($this->once())
                       ->method('translate')
                       ->with($this->equalTo($input), $this->equalTo($textDomain), $this->equalTo($locale))
                       ->will($this->returnValue($expected));

        $this->helper->setTranslator($translatorMock);

        $this->assertEquals($expected, $this->helper->__invoke($input, $textDomain, $locale));
    }
}
