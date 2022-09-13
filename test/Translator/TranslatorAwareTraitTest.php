<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\Translator;
use LaminasTest\I18n\TestCase;
use LaminasTest\I18n\Translator\TestAsset\TranslatorAwareObject;

class TranslatorAwareTraitTest extends TestCase
{
    private TranslatorAwareObject $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new TranslatorAwareObject();
    }

    public function testSetTranslator(): void
    {
        self::assertNull($this->object->getTranslator());

        $translator = new Translator();

        $this->object->setTranslator($translator);

        self::assertSame($translator, $this->object->getTranslator());
    }

    public function testSetTranslatorAndTextDomain(): void
    {
        self::assertNull($this->object->getTranslator());
        self::assertSame('default', $this->object->getTranslatorTextDomain());

        $translator = new Translator();
        $textDomain = 'domain';

        $this->object->setTranslator($translator, $textDomain);

        self::assertSame($translator, $this->object->getTranslator());
        self::assertSame('domain', $this->object->getTranslatorTextDomain());
    }

    public function testGetTranslator(): void
    {
        self::assertNull($this->object->getTranslator());

        $translator = new Translator();

        $this->object->setTranslator($translator);

        self::assertEquals($translator, $this->object->getTranslator());
    }

    public function testHasTranslator(): void
    {
        self::assertFalse($this->object->hasTranslator());

        $translator = new Translator();

        $this->object->setTranslator($translator);

        self::assertTrue($this->object->hasTranslator());
    }

    public function testSetTranslatorEnabled(): void
    {
        self::assertTrue($this->object->isTranslatorEnabled());

        $enabled = false;

        $this->object->setTranslatorEnabled($enabled);

        self::assertFalse($this->object->isTranslatorEnabled());

        $this->object->setTranslatorEnabled();

        self::assertTrue($this->object->isTranslatorEnabled());
    }

    public function testIsTranslatorEnabled(): void
    {
        self::assertTrue($this->object->isTranslatorEnabled());

        $this->object->setTranslatorEnabled(false);

        self::assertFalse($this->object->isTranslatorEnabled());
    }

    public function testSetTranslatorTextDomain(): void
    {
        self::assertSame('default', $this->object->getTranslatorTextDomain());

        $textDomain = 'domain';

        $this->object->setTranslatorTextDomain($textDomain);

        self::assertSame('domain', $this->object->getTranslatorTextDomain());
    }

    public function testGetTranslatorTextDomain(): void
    {
        self::assertEquals('default', $this->object->getTranslatorTextDomain());

        $textDomain = 'domain';

        $this->object->setTranslatorTextDomain($textDomain);

        self::assertEquals($textDomain, $this->object->getTranslatorTextDomain());
    }
}
