<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\View\Helper\TranslatePlural as TranslatePluralHelper;
use LaminasTest\I18n\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class TranslatePluralTest extends TestCase
{
    use ProphecyTrait;

    /** @var TranslatePluralHelper */
    public $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new TranslatePluralHelper();
    }

    public function testInvokingWithoutTranslatorWillRaiseException()
    {
        $this->expectException(RuntimeException::class);
        $this->helper->__invoke('singular', 'plural', 1);
    }

    public function testDefaultInvokeArguments()
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';

        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translatePlural($singularInput, $pluralInput, $numberInput, 'default', null)
            ->willReturn($expected)
            ->shouldBeCalledTimes(1);

        $this->helper->setTranslator($translatorMock->reveal());

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

        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translatePlural($singularInput, $pluralInput, $numberInput, $textDomain, $locale)
            ->willReturn($expected)
            ->shouldBeCalledTimes(1);

        $this->helper->setTranslator($translatorMock->reveal());

        $this->assertEquals($expected, $this->helper->__invoke(
            $singularInput,
            $pluralInput,
            $numberInput,
            $textDomain,
            $locale
        ));
    }
}
