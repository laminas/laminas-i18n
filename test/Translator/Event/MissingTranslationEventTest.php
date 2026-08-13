<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Event;

use Laminas\I18n\Translator\Event\MissingTranslationEvent;
use PHPUnit\Framework\TestCase;

final class MissingTranslationEventTest extends TestCase
{
    public function testGettersReturnConstructorArguments(): void
    {
        $event = new MissingTranslationEvent(
            'foo',
            'de_DE',
            'bar'
        );

        self::assertSame('foo', $event->getMessage());
        self::assertSame('de_DE', $event->getLocale());
        self::assertSame('bar', $event->getTextDomain());
    }

    public function testPropagationIsMutableBasedOnTranslationPresence(): void
    {
        $event = new MissingTranslationEvent(
            'message-id',
            'en_US',
            'default'
        );

        self::assertNull($event->getTranslation());
        self::assertFalse($event->isPropagationStopped());

        $event->setTranslation('Translated Message');

        self::assertSame('Translated Message', $event->getTranslation());
        self::assertTrue($event->isPropagationStopped());
    }
}
