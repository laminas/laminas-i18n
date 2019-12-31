<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\TranslatePlural as TranslatePluralHelper;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class TranslatePluralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatePluralHelper
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
        if (! interface_exists('Laminas\View\Helper\HelperInterface')) {
            $this->markTestSkipped(
                'Skipping tests that utilize laminas-view until that component is '
                . 'forwards-compatible with laminas-stdlib and laminas-servicemanager v3'
            );
        }

        $this->helper = new TranslatePluralHelper();
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
        $this->setExpectedException('Laminas\I18n\Exception\RuntimeException');
        $this->helper->__invoke('singular', 'plural', 1);
    }

    public function testDefaultInvokeArguments()
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';

        $translatorMock = $this->getMock('Laminas\I18n\Translator\Translator');
        $translatorMock->expects($this->once())
                       ->method('translatePlural')
                       ->with(
                           $this->equalTo($singularInput),
                           $this->equalTo($pluralInput),
                           $this->equalTo($numberInput),
                           $this->equalTo('default'),
                           $this->equalTo(null)
                       )
                       ->will($this->returnValue($expected));

        $this->helper->setTranslator($translatorMock);

        $this->assertEquals($expected, $this->helper->__invoke($singularInput, $pluralInput, $numberInput));
    }

    public function testCustomInvokeArguments()
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';
        $textDomain    = 'textDomain';
        $locale        = 'en_US';

        $translatorMock = $this->getMock('Laminas\I18n\Translator\Translator');
        $translatorMock->expects($this->once())
                       ->method('translatePlural')
                       ->with(
                           $this->equalTo($singularInput),
                           $this->equalTo($pluralInput),
                           $this->equalTo($numberInput),
                           $this->equalTo($textDomain),
                           $this->equalTo($locale)
                       )
                       ->will($this->returnValue($expected));

        $this->helper->setTranslator($translatorMock);

        $this->assertEquals($expected, $this->helper->__invoke(
            $singularInput,
            $pluralInput,
            $numberInput,
            $textDomain,
            $locale
        ));
    }
}
