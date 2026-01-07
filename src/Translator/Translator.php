<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\Value\TranslationFile;
use Laminas\I18n\Translator\Value\TranslatorFilePattern;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Translator\TranslatorInterface;
use Locale;
use Psr\Cache\CacheItemPoolInterface;
use Traversable;

use function array_shift;
use function assert;
use function get_debug_type;
use function is_array;
use function is_file;
use function is_string;
use function md5;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Translator.
 *
 * @psalm-type FileList = array<non-empty-string, array<non-empty-string, list<TranslationFile>>>
 * @psalm-type FilePatternList = array<non-empty-string, list<TranslatorFilePattern>>
 * @final
 */
class Translator implements TranslatorInterface
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

    /**
     * Files used for loading messages.
     *
     * @var FileList
     */
    private array $files = [];

    /**
     * Patterns used for loading messages.
     *
     * @var FilePatternList
     */
    private array $patterns = [];

    /**
     * Remote locations for loading messages.
     *
     * @var array<non-empty-string, list<non-empty-string>>
     */
    private array $remote = [];

    /**
     * Default locale.
     *
     * @var non-empty-string|null
     */
    private string|null $locale = null;

    /**
     * Locale to use as fallback if there is no translation.
     *
     * @var non-empty-string|null
     */
    private string|null $fallbackLocale = null;

    private CacheItemPoolInterface|null $cache = null;

    private readonly EventManagerInterface $events;
    private bool $eventsEnabled = false;

    public function __construct(
        private readonly MessageLoaderPluginManagerInterface $pluginManager,
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
     * Instantiate a translator
     *
     * @param  array|Traversable $options
     * @return static
     * @throws Exception\InvalidArgumentException
     */
    public static function factory(MessageLoaderPluginManagerInterface $pluginManager, $options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object; received "%s"',
                __METHOD__,
                get_debug_type($options),
            ));
        }

        $translator = new self($pluginManager);

        // locales
        if (isset($options['locale'])) {
            $locales = (array) $options['locale'];
            $translator->setLocale(array_shift($locales));
            if ($locales) {
                $translator->setFallbackLocale(array_shift($locales));
            }
        }

        // file patterns
        if (isset($options['translation_file_patterns'])) {
            if (! is_array($options['translation_file_patterns'])) {
                throw new Exception\InvalidArgumentException(
                    '"translation_file_patterns" should be an array',
                );
            }

            foreach ($options['translation_file_patterns'] as $spec) {
                if (! is_array($spec)) {
                    continue;
                }

                $pattern = TranslatorFilePattern::fromArray(
                    $spec,
                    self::DEFAULT_TEXT_DOMAIN,
                );

                $translator->patterns[$pattern->textDomain][] = $pattern;
            }
        }

        // files
        if (isset($options['translation_files'])) {
            if (! is_array($options['translation_files'])) {
                throw new Exception\InvalidArgumentException(
                    '"translation_files" should be an array',
                );
            }

            /** @psalm-var mixed $spec */
            foreach ($options['translation_files'] as $spec) {
                if (! is_array($spec)) {
                    continue;
                }

                $file = TranslationFile::fromArray($spec, self::ANY_LOCALE, self::DEFAULT_TEXT_DOMAIN);

                $translator->files[$file->textDomain]                ??= [];
                $translator->files[$file->textDomain][$file->locale] ??= [];
                $translator->files[$file->textDomain][$file->locale][] = $file;
            }
        }

        // remote
        if (isset($options['remote_translation'])) {
            if (! is_array($options['remote_translation'])) {
                throw new Exception\InvalidArgumentException(
                    '"remote_translation" should be an array',
                );
            }

            $requiredKeys = ['type'];
            foreach ($options['remote_translation'] as $remote) {
                foreach ($requiredKeys as $key) {
                    if (! isset($remote[$key])) {
                        throw new Exception\InvalidArgumentException(
                            "'{$key}' is missing for remote translation options",
                        );
                    }
                }

                $translator->addRemoteTranslations(
                    $remote['type'],
                    $remote['text_domain'] ?? self::DEFAULT_TEXT_DOMAIN,
                );
            }
        }

        // cache
        if (isset($options['cache'])) {
            if ($options['cache'] instanceof CacheItemPoolInterface) {
                $translator->setCache($options['cache']);
            }
        }

        // event manager enabled
        if (isset($options['event_manager_enabled']) && $options['event_manager_enabled']) {
            $translator->enableEventManager();
        }

        return $translator;
    }

    /**
     * Set the default locale.
     *
     * @param non-empty-string $locale
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the default locale.
     *
     * @return non-empty-string
     */
    public function getLocale(): string
    {
        if ($this->locale === null) {
            $default = Locale::getDefault();
            assert($default !== '');

            $this->locale = $default;
        }

        return $this->locale;
    }

    /**
     * Set the fallback locale.
     *
     * @param non-empty-string|null $locale
     * @return $this
     */
    public function setFallbackLocale(string|null $locale): self
    {
        $this->fallbackLocale = $locale;

        return $this;
    }

    /**
     * Get the fallback locale.
     */
    public function getFallbackLocale(): string|null
    {
        return $this->fallbackLocale;
    }

    public function setCache(CacheItemPoolInterface|null $cache = null): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache(): CacheItemPoolInterface|null
    {
        return $this->cache;
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
        $locale    ??= $this->getLocale();
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
        $locale    ??= $this->getLocale();
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

            $last = $results->last();
            if (is_string($last)) {
                return $last;
            }
        }

        return null;
    }

    /**
     * Add a translation file.
     *
     * @param non-empty-string $type
     * @param non-empty-string $filename
     * @param non-empty-string|null $textDomain
     * @param non-empty-string|null $locale
     * @return $this
     */
    public function addTranslationFile(
        string $type,
        string $filename,
        string|null $textDomain = null,
        string|null $locale = null,
    ): self {
        $file = TranslationFile::fromArray(
            [
                'type'     => $type,
                'filename' => $filename,
            ],
            $locale ?? self::ANY_LOCALE,
            $textDomain ?? self::DEFAULT_TEXT_DOMAIN,
        );

        $this->files[$file->textDomain]                ??= [];
        $this->files[$file->textDomain][$file->locale] ??= [];
        $this->files[$file->textDomain][$file->locale][] = $file;

        return $this;
    }

    /**
     * Add multiple translations with a file pattern.
     *
     * @param non-empty-string $type
     * @param non-empty-string $baseDir
     * @param non-empty-string $pattern
     * @param non-empty-string $textDomain
     * @return $this
     */
    public function addTranslationFilePattern(
        string $type,
        string $baseDir,
        string $pattern,
        string $textDomain = self::DEFAULT_TEXT_DOMAIN,
    ): self {
        $pattern = new TranslatorFilePattern(
            $type,
            $baseDir,
            $pattern,
            $textDomain,
        );

        $this->patterns[$pattern->textDomain] ??= [];
        $this->patterns[$pattern->textDomain][] = $pattern;

        return $this;
    }

    /**
     * Add remote translations.
     *
     * @param non-empty-string $type
     * @param non-empty-string $textDomain
     * @return $this
     */
    public function addRemoteTranslations(string $type, string $textDomain = self::DEFAULT_TEXT_DOMAIN): self
    {
        if (! isset($this->remote[$textDomain])) {
            $this->remote[$textDomain] = [];
        }

        $this->remote[$textDomain][] = $type;

        return $this;
    }

    /**
     * Get the cache identifier for a specific textDomain and locale.
     *
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function getCacheId($textDomain, $locale)
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
     * @throws Exception\RuntimeException
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

        $messagesLoaded  = 0;
        $messagesLoaded |= (int) $this->loadMessagesFromRemote($textDomain, $locale);
        $messagesLoaded |= (int) $this->loadMessagesFromPatterns($textDomain, $locale);
        $messagesLoaded |= (int) $this->loadMessagesFromFiles($textDomain, $locale);

        if ($messagesLoaded === 0) {
            $discoveredTextDomain = null;
            if ($this->isEventManagerEnabled()) {
                $until = static fn(mixed $r): bool => $r instanceof TextDomain;

                $event = new Event(self::EVENT_NO_MESSAGES_LOADED, $this, [
                    'locale'      => $locale,
                    'text_domain' => $textDomain,
                ]);

                $results = $this->getEventManager()->triggerEventUntil($until, $event);

                $last = $results->last();
                if ($last instanceof TextDomain) {
                    $discoveredTextDomain = $last;
                }
            }

            $this->insertLoadedTextDomain($discoveredTextDomain, $textDomain, $locale);
        }

        if ($this->cache !== null) {
            $cacheId = $this->getCacheId($textDomain, $locale);
            $item    = $this->cache->getItem($cacheId);
            $item->set($this->messages[$textDomain][$locale]);
            $this->cache->save($item);
        }
    }

    /**
     * Load messages from remote sources.
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws Exception\RuntimeException When specified loader is not a remote loader.
     */
    private function loadMessagesFromRemote(string $textDomain, string $locale): bool
    {
        $messagesLoaded = false;

        if (isset($this->remote[$textDomain])) {
            foreach ($this->remote[$textDomain] as $loaderType) {
                $loader = $this->pluginManager->get($loaderType);

                if (! $loader instanceof RemoteLoaderInterface) {
                    throw new Exception\RuntimeException('Specified loader is not a remote loader');
                }

                $this->insertLoadedTextDomain(
                    $loader->load($locale, $textDomain),
                    $textDomain,
                    $locale,
                );

                $messagesLoaded = true;
            }
        }

        return $messagesLoaded;
    }

    /**
     * Load messages from patterns.
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws Exception\RuntimeException When specified loader is not a file loader.
     */
    private function loadMessagesFromPatterns(string $textDomain, string $locale): bool
    {
        $messagesLoaded = false;

        if (isset($this->patterns[$textDomain])) {
            foreach ($this->patterns[$textDomain] as $pattern) {
                $filename = sprintf(
                    '%s%s%s',
                    $pattern->baseDirectory,
                    DIRECTORY_SEPARATOR,
                    sprintf($pattern->pattern, $locale),
                );

                if (! is_file($filename)) {
                    continue;
                }

                $loader = $this->pluginManager->get($pattern->type);

                if (! $loader instanceof FileLoaderInterface) {
                    throw new Exception\RuntimeException('Specified loader is not a file loader');
                }

                $this->insertLoadedTextDomain(
                    $loader->load($locale, $filename),
                    $textDomain,
                    $locale,
                );

                $messagesLoaded = true;
            }
        }

        return $messagesLoaded;
    }

    /**
     * Load messages from files.
     *
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws Exception\RuntimeException When specified loader is not a file loader.
     */
    private function loadMessagesFromFiles(string $textDomain, string $locale): bool
    {
        $messagesLoaded = false;

        foreach ([$locale, self::ANY_LOCALE] as $currentLocale) {
            if (! isset($this->files[$textDomain][$currentLocale])) {
                continue;
            }

            foreach ($this->files[$textDomain][$currentLocale] as $file) {
                $loader = $this->pluginManager->get($file->type);

                if (! $loader instanceof FileLoaderInterface) {
                    throw new Exception\RuntimeException('Specified loader is not a file loader');
                }

                $this->insertLoadedTextDomain(
                    $loader->load($locale, $file->filename),
                    $textDomain,
                    $locale,
                );

                $messagesLoaded = true;
            }

            unset($this->files[$textDomain][$currentLocale]);
        }

        return $messagesLoaded;
    }

    /**
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     */
    private function insertLoadedTextDomain(TextDomain|null $data, string $textDomain, string $locale): void
    {
        $this->messages[$textDomain] ??= [];

        if ($data === null) {
            $this->messages[$textDomain][$locale] ??= null;

            return;
        }

        if (isset($this->messages[$textDomain][$locale])) {
            $this->messages[$textDomain][$locale]->merge($data);

            return;
        }

        $this->messages[$textDomain][$locale] = $data;
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
