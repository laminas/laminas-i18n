<?php

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 7.3
 */
class TranslatorAwareTraitTest extends TestCase
{
    public function testSetTranslator()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertNull($object->getTranslator());

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertSame($translator, $object->getTranslator());
    }

    public function testSetTranslatorAndTextDomain()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertNull($object->getTranslator());
        $this->assertSame('default', $object->getTranslatorTextDomain());

        $translator = new Translator;
        $textDomain = 'domain';

        $object->setTranslator($translator, $textDomain);

        $this->assertSame($translator, $object->getTranslator());
        $this->assertSame('domain', $object->getTranslatorTextDomain());
    }

    public function testGetTranslator()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertNull($object->getTranslator());

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertEquals($translator, $object->getTranslator());
    }

    public function testHasTranslator()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertFalse($object->hasTranslator());

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertTrue($object->hasTranslator());
    }

    public function testSetTranslatorEnabled()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertTrue($object->isTranslatorEnabled());

        $enabled = false;

        $object->setTranslatorEnabled($enabled);

        $this->assertFalse($object->isTranslatorEnabled());

        $object->setTranslatorEnabled();

        $this->assertTrue($object->isTranslatorEnabled());
    }

    public function testIsTranslatorEnabled()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertTrue($object->isTranslatorEnabled());

        $object->setTranslatorEnabled(false);

        $this->assertFalse($object->isTranslatorEnabled());
    }

    public function testSetTranslatorTextDomain()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertSame('default', $object->getTranslatorTextDomain());

        $textDomain = 'domain';

        $object->setTranslatorTextDomain($textDomain);

        $this->assertSame('domain', $object->getTranslatorTextDomain());
    }

    public function testGetTranslatorTextDomain()
    {
        $object = $this->getObjectForTrait('\Laminas\I18n\Translator\TranslatorAwareTrait');

        $this->assertEquals('default', $object->getTranslatorTextDomain());

        $textDomain = 'domain';

        $object->setTranslatorTextDomain($textDomain);

        $this->assertEquals($textDomain, $object->getTranslatorTextDomain());
    }
}
