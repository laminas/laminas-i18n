<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\View\Helper\Translate as TranslateHelper;
use LaminasTest\I18n\TestCase;

class TranslateTest extends TestCase
{
    public TranslateHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new TranslateHelper();
    }

    public function testInvokingWithoutTranslatorWillRaiseException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->helper->__invoke('message');
    }

    public function testDefaultInvokeArguments(): void
    {
        $input    = 'input';
        $expected = 'translated';

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock->expects(self::once())
            ->method('translate')
            ->with($input, 'default', null)
            ->willReturn($expected);

        $this->helper->setTranslator($translatorMock);

        self::assertEquals($expected, $this->helper->__invoke($input));
    }

    public function testCustomInvokeArguments(): void
    {
        $input      = 'input';
        $expected   = 'translated';
        $textDomain = 'textDomain';
        $locale     = 'en_US';

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock->expects(self::once())
            ->method('translate')
            ->with($input, $textDomain, $locale)
            ->willReturn($expected);

        $this->helper->setTranslator($translatorMock);

        self::assertEquals($expected, $this->helper->__invoke($input, $textDomain, $locale));
    }
}
