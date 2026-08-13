<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Event;

use Laminas\I18n\Translator\Event\NoMessagesLoadedEvent;
use Laminas\I18n\Translator\TextDomain;
use PHPUnit\Framework\TestCase;

final class NoMessagesLoadedEventTest extends TestCase
{
    public function testGettersReturnConstructorArguments(): void
    {
        $event = new NoMessagesLoadedEvent(
            'fr_FR',
            'navigation'
        );

        self::assertSame('fr_FR', $event->getLocale());
        self::assertSame('navigation', $event->getTextDomain());
    }

    public function testPropagationIsMutableBasedOnMessagesPresence(): void
    {
        $event = new NoMessagesLoadedEvent(
            'en_US',
            'default'
        );

        self::assertNull($event->getMessages());
        self::assertFalse($event->isPropagationStopped());

        $textDomain = new TextDomain();

        $event->setMessages($textDomain);

        self::assertSame($textDomain, $event->getMessages());
        self::assertTrue($event->isPropagationStopped());
    }
}
