<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event fired when the translation for a message is missing.
 */
final class MissingTranslationEvent implements StoppableEventInterface
{
    private ?string $translation = null;

    /**
     * @param non-empty-string $message
     * @param non-empty-string $locale
     * @param non-empty-string $textDomain
     */
    public function __construct(
        private readonly string $message,
        private readonly string $locale,
        private readonly string $textDomain,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getMessage(): string
    {
        return $this->message;
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

    public function setTranslation(string $translation): void
    {
        $this->translation = $translation;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function isPropagationStopped(): bool
    {
        return $this->translation !== null;
    }
}
