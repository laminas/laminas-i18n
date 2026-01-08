<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Exception\ExceptionInterface;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\Translator\TranslatorInterface;
use Psr\Cache\CacheItemPoolInterface;

use function assert;
use function is_string;
use function md5;

final class Translator implements TranslatorInterface
{
    public const ANY_LOCALE = '*';

    /**
     * Event fired when the translation for a message is missing.
     */
    public const EVENT_MISSING_TRANSLATION = 'missingTranslation';

    /**
     * Event fired when no messages were loaded for a locale/text-domain combination.
     */
    public const EVENT_NO_MESSAGES_LOADED = 'noMessagesLoaded';

    /**
     * Messages loaded by the translator.
     *
     * @var array<non-empty-string, array<non-empty-string, TextDomain|null>>
     */
    private array $messages = [];

    private CacheItemPoolInterface|null $cache = null;

    private readonly EventManagerInterface $events;
    private bool $eventsEnabled = false;

    /**
     * @param non-empty-string $defaultLocale
     * @param non-empty-string|null $fallbackLocale
     */
    public function __construct(
        private readonly TranslationCollectorInterface $collector,
        private string $defaultLocale,
        private readonly string|null $fallbackLocale = null,
        EventManagerInterface|null $eventManager = null,
    ) {
        // When an EventManager is supplied to the constructor, enable events. The user clearly wants them!
        if ($eventManager !== null) {
            $this->eventsEnabled = true;
        }

        $eventManager ??= new EventManager();
        $eventManager->setIdentifiers([
            self::class,
            'translator',
        ]);

        $this->events = $eventManager;
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

    public function setCache(CacheItemPoolInterface|null $cache = null): self
    {
        $this->cache = $cache;

        return $this;
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
        string $textDomain = self::DEFAULT_TEXT_DOMAIN,
        string|null $locale = null,
    ): string {
        $locale    ??= $this->defaultLocale;
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
     * @param non-empty-string $textDomain
     * @param non-empty-string|null $locale
     * @psalm-suppress MoreSpecificImplementedParamType This will be redundant when Translator interface is improved
     */
    public function translatePlural(
        string $singular,
        string $plural,
        int $number,
        string $textDomain = self::DEFAULT_TEXT_DOMAIN,
        string|null $locale = null,
    ): string {
        $locale    ??= $this->defaultLocale;
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
    protected function getTranslatedMessage(
        string|null $message,
        string $locale,
        string $textDomain = self::DEFAULT_TEXT_DOMAIN,
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

        if ($this->isEventManagerEnabled()) {
            $until = static fn(mixed $r): bool => is_string($r);

            $event = new Event(self::EVENT_MISSING_TRANSLATION, $this, [
                'message'     => $message,
                'locale'      => $locale,
                'text_domain' => $textDomain,
            ]);

            $results = $this->getEventManager()->triggerEventUntil($until, $event);

            /** @psalm-var mixed $last */
            $last = $results->last();
            if (is_string($last)) {
                return $last;
            }
        }

        return null;
    }

    /**
     * Get the cache identifier for a specific textDomain and locale.
     */
    public function getCacheId(string $textDomain, string $locale): string
    {
        return 'Laminas_I18n_Translator_Messages_' . md5($textDomain . $locale);
    }

    /**
     * Clears the cache for a specific textDomain and locale.
     */
    public function clearCache(string $textDomain, string $locale): bool
    {
        if ($this->cache === null) {
            return false;
        }

        return $this->cache->deleteItem($this->getCacheId($textDomain, $locale));
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

        if ($this->cache !== null) {
            $cacheId = $this->getCacheId($textDomain, $locale);
            $item    = $this->cache->getItem($cacheId);
            if ($item->isHit()) {
                $value = $item->get();
                assert($value instanceof TextDomain);

                $this->messages[$textDomain][$locale] = $value;

                return;
            }
        }

        $messages = $this->collector->collect($textDomain, $locale);

        $messagesLoaded = $messages->count();

        if ($messagesLoaded === 0) {
            if ($this->isEventManagerEnabled()) {
                $until = static fn(mixed $r): bool => $r instanceof TextDomain;

                $event = new Event(self::EVENT_NO_MESSAGES_LOADED, $this, [
                    'locale'      => $locale,
                    'text_domain' => $textDomain,
                ]);

                $results = $this->getEventManager()->triggerEventUntil($until, $event);

                /** @psalm-var mixed $last */
                $last = $results->last();
                if ($last instanceof TextDomain) {
                    $messages = $last;
                }
            }
        }

        $this->messages[$textDomain][$locale] = $messages;

        if ($this->cache !== null) {
            $cacheId = $this->getCacheId($textDomain, $locale);
            $item    = $this->cache->getItem($cacheId);
            $item->set($messages);
            $this->cache->save($item);
        }
    }

    /**
     * Return all the messages.
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string|null $locale
     */
    public function getAllMessages(
        string $textDomain = self::DEFAULT_TEXT_DOMAIN,
        string|null $locale = null,
    ): TextDomain|null {
        $locale ??= $this->getLocale();

        if (! isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }

        return $this->messages[$textDomain][$locale];
    }

    public function getEventManager(): EventManagerInterface
    {
        return $this->events;
    }

    /**
     * Check whether the event manager is enabled.
     */
    public function isEventManagerEnabled(): bool
    {
        return $this->eventsEnabled;
    }

    /**
     * Enable the event manager.
     *
     * @return $this
     */
    public function enableEventManager(): self
    {
        $this->eventsEnabled = true;
        return $this;
    }

    /**
     * Disable the event manager.
     *
     * @return $this
     */
    public function disableEventManager(): self
    {
        $this->eventsEnabled = false;
        return $this;
    }
}
