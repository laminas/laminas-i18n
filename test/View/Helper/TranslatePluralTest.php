<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\View\Helper\TranslatePlural as TranslatePluralHelper;
use LaminasTest\I18n\TestCase;

class TranslatePluralTest extends TestCase
{
    /** @var TranslatePluralHelper */
    public $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new TranslatePluralHelper();
    }

    public function testInvokingWithoutTranslatorWillRaiseException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->helper->__invoke('singular', 'plural', 1);
    }

    public function testDefaultInvokeArguments(): void
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock->expects(self::once())
            ->method('translatePlural')
            ->with($singularInput, $pluralInput, $numberInput, 'default', null)
            ->willReturn($expected);

        $this->helper->setTranslator($translatorMock);

        self::assertEquals($expected, $this->helper->__invoke($singularInput, $pluralInput, $numberInput));
    }

    public function testCustomInvokeArguments(): void
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';
        $textDomain    = 'textDomain';
        $locale        = 'en_US';

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock->expects(self::once())
            ->method('translatePlural')
            ->with($singularInput, $pluralInput, $numberInput, $textDomain, $locale)
            ->willReturn($expected);

        $this->helper->setTranslator($translatorMock);

        self::assertEquals($expected, $this->helper->__invoke(
            $singularInput,
            $pluralInput,
            $numberInput,
            $textDomain,
            $locale
        ));
    }
}
