<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Translator\Translator;
use Laminas\I18n\View\Helper\Translate as TranslateHelper;
use LaminasTest\I18n\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class TranslateTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var TranslateHelper
     */
    public $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new TranslateHelper();
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

        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translate($input, 'default', null)
            ->willReturn($expected)
            ->shouldBeCalledTimes(1);

        $this->helper->setTranslator($translatorMock->reveal());

        $this->assertEquals($expected, $this->helper->__invoke($input));
    }

    public function testCustomInvokeArguments()
    {
        $input      = 'input';
        $expected   = 'translated';
        $textDomain = 'textDomain';
        $locale     = 'en_US';

        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translate($input, $textDomain, $locale)
            ->willReturn($expected)
            ->shouldBeCalledTimes(1);

        $this->helper->setTranslator($translatorMock->reveal());

        $this->assertEquals($expected, $this->helper->__invoke($input, $textDomain, $locale));
    }
}
