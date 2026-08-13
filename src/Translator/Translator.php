<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\I18n\Exception\ExceptionInterface;
use Laminas\I18n\Translator\Event\MissingTranslationEvent;
use Laminas\I18n\Translator\Event\NoMessagesLoadedEvent;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\Translator\TranslatorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use function is_string;

final class Translator implements TranslatorInterface
{
    public const ANY_LOCALE = '*';
    /**
     * Messages loaded by the translator.
     *
     * @var array<non-empty-string, array<non-empty-string, TextDomain|null>>
     */
    private array $messages = [];

    /**
     * @param non-empty-string $defaultLocale
     * @param non-empty-string|null $fallbackLocale
     * @param non-empty-string $defaultTextDomain
     */
    public function __construct(
        private readonly TranslationCollectorInterface $collector,
        private string $defaultLocale,
        private readonly string|null $fallbackLocale = null,
        private readonly string $defaultTextDomain = TranslatorInterface::DEFAULT_TEXT_DOMAIN,
        private readonly EventDispatcherInterface|null $events = null,
    ) {
    }

    /**
     * Set the default locale.
     *
     * @param non-empty-string $defaultLocale
     * @return $this
     */
    public function setLocale(string $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * Get the default locale.
     *
     * @return non-empty-string
     */
    public function getLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Translate a message.
     *
     * @param non-empty-string $message
     * @param non-empty-string $textDomain
     * @param non-empty-string|null $locale
     * @psalm-suppress MoreSpecificImplementedParamType This will be redundant when Translator interface is improved
     */
    public function translate(
        string $message,
        string|null $textDomain = null,
        string|null $locale = null,
    ): string {
        $locale     ??= $this->defaultLocale;
        $textDomain ??= $this->defaultTextDomain;
        $translation = $this->getTranslatedMessage($message, $locale, $textDomain);

        if (is_string($translation) && $translation !== '') {
            return $translation;
        }

        if ($this->fallbackLocale !== null && $locale !== $this->fallbackLocale) {
            return $this->translate($message, $textDomain, $this->fallbackLocale);
        }

        return $message;
    }

    /**
     * Translate a plural message.
     *
     * @param non-empty-string|null $textDomain
     * @param non-empty-string|null $locale
     * @psalm-suppress MoreSpecificImplementedParamType This will be redundant when Translator interface is improved
     */
    public function translatePlural(
        string $singular,
        string $plural,
        int $number,
        string|null $textDomain = null,
        string|null $locale = null,
    ): string {
        $locale     ??= $this->defaultLocale;
        $textDomain ??= $this->defaultTextDomain;
        $translation = $this->getTranslatedMessage($singular, $locale, $textDomain);

        if (is_string($translation)) {
            $translation = [$translation];
        }

        $index = $number === 1 ? 0 : 1; // en_EN Plural rule
        if ($this->messages[$textDomain][$locale] instanceof TextDomain) {
            $index = $this->messages[$textDomain][$locale]
                ->getPluralRule()->evaluate($number);
        }

        if (isset($translation[$index]) && $translation[$index] !== '' && $translation[$index] !== null) {
            return $translation[$index];
        }

        if ($this->fallbackLocale !== null && $locale !== $this->fallbackLocale) {
            return $this->translatePlural(
                $singular,
                $plural,
                $number,
                $textDomain,
                $this->fallbackLocale,
            );
        }

        return $index === 0 ? $singular : $plural;
    }

    /**
     * Get a translated message.
     *
     * @triggers getTranslatedMessage.missing-translation
     * @param non-empty-string $locale
     * @param non-empty-string $textDomain
     * @return string|null|list<string|null>
     */
    private function getTranslatedMessage(
        string|null $message,
        string $locale,
        string $textDomain,
    ): string|array|null {
        if ($message === '' || $message === null) {
            return null;
        }

        if (! isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }

        if (isset($this->messages[$textDomain][$locale][$message])) {
            return $this->messages[$textDomain][$locale][$message];
        }

        /**
         * issue https://github.com/zendframework/zend-i18n/issues/53
         *
         * storage: [
         *   "default\x04Welcome" => "Cześć",
         *   "default\x04Top %s Product" => [
         *     0 => "Top %s Produkt"
         *     1 => "Top %s Produkty"
         *     2 => "Top %s Produktów"
         *   ],
         *   "Top %s Products" => "",
         * ]
         */
        if (isset($this->messages[$textDomain][$locale][$textDomain . "\x04" . $message])) {
            return $this->messages[$textDomain][$locale][$textDomain . "\x04" . $message];
        }

        if ($this->events === null) {
            return null;
        }

        $event = $this->events->dispatch(new MissingTranslationEvent($message, $locale, $textDomain));
        if (! $event instanceof MissingTranslationEvent) {
            return null;
        }

        $translation = $event->getTranslation();

        if (is_string($translation) && $translation !== '') {
            return $translation;
        }

        return null;
    }

    /**
     * Load messages for a given language and domain.
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @triggers loadMessages.no-messages-loaded
     * @throws ExceptionInterface If a problem occurs during loading of messages.
     */
    private function loadMessages(string $textDomain, string $locale): void
    {
        $this->messages[$textDomain] ??= [];

        $messages = $this->collector->collect($textDomain, $locale);

        $this->messages[$textDomain][$locale] = $messages;

        if ($messages->count() > 0) {
            return;
        }

        if ($this->events === null) {
            return;
        }

        $event = $this->events->dispatch(new NoMessagesLoadedEvent($locale, $textDomain));

        if (! $event instanceof NoMessagesLoadedEvent) {
            return;
        }

        $messages = $event->getMessages();
        if (! $messages instanceof TextDomain) {
            return;
        }

        // Override with fallback messages if the event successfully provided them
        $this->messages[$textDomain][$locale] = $messages;
    }

    /**
     * Return all the messages.
     *
     * @param non-empty-string|null $textDomain
     * @param non-empty-string|null $locale
     */
    public function getAllMessages(
        string|null $textDomain = null,
        string|null $locale = null,
    ): TextDomain|null {
        $locale     ??= $this->getLocale();
        $textDomain ??= $this->defaultTextDomain;

        if (! isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }

        return $this->messages[$textDomain][$locale];
    }

    public function getEventDispatcher(): EventDispatcherInterface|null
    {
        return $this->events;
    }
}
