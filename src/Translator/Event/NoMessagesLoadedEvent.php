<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Event;

use Laminas\I18n\Translator\TextDomain;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event fired when no messages were loaded for a locale/text-domain combination.
 */
final class NoMessagesLoadedEvent implements StoppableEventInterface
{
    private ?TextDomain $messages = null;

    /**
     * @param non-empty-string $locale
     * @param non-empty-string $textDomain
     */
    public function __construct(
        private readonly string $locale,
        private readonly string $textDomain,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return non-empty-string
     */
    public function getTextDomain(): string
    {
        return $this->textDomain;
    }

    public function setMessages(TextDomain $messages): void
    {
        $this->messages = $messages;
    }

    public function getMessages(): ?TextDomain
    {
        return $this->messages;
    }

    public function isPropagationStopped(): bool
    {
        return $this->messages !== null;
    }
}
