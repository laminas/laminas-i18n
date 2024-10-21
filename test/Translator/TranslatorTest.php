<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\StorageFactory as CacheFactory;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventInterface;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\Translator;
use LaminasTest\I18n\TestCase;
use LaminasTest\I18n\Translator\TestAsset\Loader as TestLoader;
use Locale;

class TranslatorTest extends TestCase
{
    private Translator $translator;
    private string $testFilesDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translator = new Translator();
        Locale::setDefault('en_EN');
        $this->testFilesDir = __DIR__ . '/_files';
    }

    public function testFactoryCreatesTranslator(): void
    {
        $translator = Translator::factory([
            'locale'   => 'de_DE',
            'patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-%s.php',
                ],
            ],
            'files'    => [
                [
                    'type'     => 'phparray',
                    'filename' => $this->testFilesDir . '/translation_en.php',
                ],
            ],
        ]);

        self::assertInstanceOf(Translator::class, $translator);
        self::assertEquals('de_DE', $translator->getLocale());
    }

    public function testTranslationFromSeveralTranslationFiles(): void
    {
        $translator = Translator::factory([
            'locale'                    => 'de_DE',
            'translation_file_patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-%s.php',
                ],
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-more-%s.php',
                ],
            ],
        ]);

        //Test translator instance
        self::assertInstanceOf(Translator::class, $translator);

        //Test translations
        self::assertEquals(
            'Nachricht 1',
            $translator->translate('Message 1')
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 9',
            $translator->translate('Message 9')
        ); //translation-more-de_DE.php
        self::assertEquals(
            'Nachricht 10 - 0',
            $translator->translatePlural('Message 10', 'Message 10', 1)
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 10 - 1',
            $translator->translatePlural('Message 10', 'Message 10', 2)
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 11 - 0',
            $translator->translatePlural('Message 11', 'Message 11', 1)
        ); //translation-more-de_DE.php
        self::assertEquals(
            'Nachricht 11 - 1',
            $translator->translatePlural('Message 11', 'Message 11', 2)
        ); //translation-more-de_DE.php
    }

    public function testTranslationFromDifferentSourceTypes(): void
    {
        $translator = Translator::factory([
            'locale'                    => 'de_DE',
            'translation_file_patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-de_DE.php',
                ],
            ],
            'translation_files'         => [
                [
                    'type'     => 'phparray',
                    'filename' => $this->testFilesDir . '/testarray/translation-more-de_DE.php',
                ],
            ],
        ]);

        self::assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        self::assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
    }

    public function testFactoryCreatesTranslatorWithCache(): void
    {
        $translator = Translator::factory([
            'locale'   => 'de_DE',
            'patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-%s.php',
                ],
            ],
            'cache'    => [
                'adapter' => 'memory',
            ],
        ]);

        self::assertInstanceOf(Translator::class, $translator);
        self::assertInstanceOf(StorageInterface::class, $translator->getCache());
    }

    public function testDefaultLocale(): void
    {
        self::assertEquals('en_EN', $this->translator->getLocale());
    }

    public function testForcedLocale(): void
    {
        $this->translator->setLocale('de_DE');
        self::assertEquals('de_DE', $this->translator->getLocale());
    }

    public function testTranslate(): void
    {
        $loader             = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $pm                 = $this->translator->getPluginManager();
        $pm->configure([
            'services' => [
                'test' => $loader,
            ],
        ]);
        $this->translator->setPluginManager($pm);
        $this->translator->addTranslationFile('test', null);

        self::assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsLoadedFromCache(): void
    {
        $cache = CacheFactory::factory(['adapter' => 'memory']);
        $this->translator->setCache($cache);

        $cache->addItem(
            $this->translator->getCacheId('default', 'en_EN'),
            new TextDomain(['foo' => 'bar'])
        );

        self::assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsAreStoredInCache(): void
    {
        $cache = CacheFactory::factory(['adapter' => 'memory']);
        $this->translator->setCache($cache);

        $loader             = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $plugins            = $this->translator->getPluginManager();
        $plugins->configure(['services' => ['test' => $loader]]);
        $this->translator->setPluginManager($plugins);
        $this->translator->addTranslationFile('test', null);

        self::assertEquals('bar', $this->translator->translate('foo'));

        $item = $cache->getItem($this->translator->getCacheId('default', 'en_EN'));
        self::assertInstanceOf(TextDomain::class, $item);
        self::assertEquals('bar', $item['foo']);
    }

    public function testTranslationsAreClearedFromCache(): void
    {
        $textDomain = 'default';
        $locale     = 'en_EN';

        $cache = CacheFactory::factory(['adapter' => 'memory']);
        $this->translator->setCache($cache);

        $cache->addItem(
            $this->translator->getCacheId($textDomain, $locale),
            new TextDomain(['foo' => 'bar'])
        );

        self::assertTrue($this->translator->clearCache($textDomain, $locale));

        $item = $cache->getItem($this->translator->getCacheId($textDomain, $locale), $success);
        self::assertNull($item);
        self::assertFalse($success);
    }

    public function testClearCacheReturnsFalseIfNoCacheIsPresent(): void
    {
        self::assertFalse($this->translator->clearCache('default', 'en_EN'));
    }

    public function testTranslatePlurals(): void
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $pl0 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        self::assertEquals('Message 5 (en) Plural 0', $pl0);
        self::assertEquals('Message 5 (en) Plural 1', $pl1);
        self::assertEquals('Message 5 (en) Plural 2', $pl2);
    }

    public function testTranslatePluralsNonExistentLocale(): void
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        $this->translator->setLocale('es_ES');

        $pl0 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        self::assertEquals('Message 5', $pl0);
        self::assertEquals('Message 5 Plural', $pl1);
        self::assertEquals('Message 5 Plural', $pl2);
    }

    public function testTranslatePluralsNonExistentTranslation(): void
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        $this->translator->setLocale('de_DE');

        $pl0 = $this->translator->translatePlural('Message 12', 'Message 12 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 12', 'Message 12 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 12', 'Message 12 Plural', 10);

        self::assertEquals('Message 12', $pl0);
        self::assertEquals('Message 12 Plural', $pl1);
        self::assertEquals('Message 12 Plural', $pl2);
    }

    public function testTranslateNoPlurals(): void
    {
        // Some languages such as Japanese and Chinese does not have plural forms
        $this->translator->setLocale('ja_JP');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/testarray/translation-noplural-ja_JP.php',
            'default',
            'ja_JP'
        );

        $pl0 = $this->translator->translatePlural('Message 9', 'Message 9 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 9', 'Message 9 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 9', 'Message 9 Plural', 10);

        self::assertEquals('Message 9 (ja)', $pl0);
        self::assertEquals('Message 9 (ja)', $pl1);
        self::assertEquals('Message 9 (ja)', $pl2);
    }

    public function testTranslateNonExistentLocale(): void
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        // Test that a locale without translations does not cause warnings

        $this->translator->setLocale('es_ES');

        self::assertEquals('Message 1', $this->translator->translate('Message 1'));
        self::assertEquals('Message 9', $this->translator->translate('Message 9'));

        $this->translator->setLocale('fr_FR');

        self::assertEquals('Message 1', $this->translator->translate('Message 1'));
        self::assertEquals('Message 9', $this->translator->translate('Message 9'));
    }

    public function testTranslateNonExistentTranslation(): void
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        // Test that a locale without translations does not cause warnings

        $this->translator->setLocale('de_DE');

        self::assertEquals('Message 13', $this->translator->translate('Message 13'));
    }

    public function testEnableDisableEventManger(): void
    {
        self::assertFalse($this->translator->isEventManagerEnabled(), 'Default value');

        $this->translator->enableEventManager();
        self::assertTrue($this->translator->isEventManagerEnabled());

        $this->translator->disableEventManager();
        self::assertFalse($this->translator->isEventManagerEnabled());
    }

    public function testEnableEventMangerViaFactory(): void
    {
        $translator = Translator::factory([
            'event_manager_enabled' => true,
        ]);
        self::assertTrue($translator->isEventManagerEnabled());

        $translator = Translator::factory([]);
        self::assertFalse($translator->isEventManagerEnabled());
    }

    public function testMissingTranslationEvent(): void
    {
        $actualEvent = null;

        $this->translator->enableEventManager();
        $this->translator->getEventManager()->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            }
            // @codingStandardsIgnoreEnd
        );

        $this->translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(Event::class, $actualEvent);
        self::assertEquals(
            [
                'message'     => 'foo',
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ],
            $actualEvent->getParams()
        );

        // But fire no event when disabled
        $actualEvent = null;
        $this->translator->disableEventManager();
        $this->translator->translate('foo', 'bar', 'baz');
        self::assertNull($actualEvent);
    }

    public function testListenerOnMissingTranslationEventCanReturnString(): void
    {
        $trigger      = null;
        $doNotTrigger = null;

        $this->translator->enableEventManager();
        $events = $this->translator->getEventManager();
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function () use (&$trigger) {
                $trigger = true;
            }
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static fn() => 'EVENT TRIGGERED'
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function () use (&$doNotTrigger) {
                $doNotTrigger = true;
            }
        );

        $result = $this->translator->translate('foo', 'bar', 'baz');
        self::assertTrue($trigger);
        self::assertEquals('EVENT TRIGGERED', $result);
        self::assertNull($doNotTrigger);
    }

    public function testNoMessagesLoadedEvent(): void
    {
        $actualEvent = null;

        $this->translator->enableEventManager();
        $this->translator
            ->getEventManager()
            ->attach(Translator::EVENT_NO_MESSAGES_LOADED, function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            });

        $this->translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(Event::class, $actualEvent);
        self::assertEquals(
            [
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ],
            $actualEvent->getParams()
        );

        // But fire no event when disabled
        $actualEvent = null;
        $this->translator->disableEventManager();
        $this->translator->translate('foo', 'bar', 'baz');
        self::assertNull($actualEvent);
    }

    public function testListenerOnNoMessagesLoadedEventCanReturnTextDomainObject(): void
    {
        $trigger      = null;
        $doNotTrigger = null;
        $textDomain   = new TextDomain([
            'foo' => 'BOOYAH',
        ]);

        $this->translator->enableEventManager();
        $events = $this->translator->getEventManager();
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static function () use (&$trigger): void {
                $trigger = true;
            }
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static fn() => $textDomain
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static function () use (&$doNotTrigger) {
                $doNotTrigger = true;
            }
        );

        $result = $this->translator->translate('foo', 'bar', 'baz');

        self::assertTrue($trigger);
        self::assertNull($doNotTrigger);
        self::assertEquals('BOOYAH', $result);
    }

    public function testGetAllMessagesLoadedInTranslator(): void
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages();
        self::assertInstanceOf(TextDomain::class, $allMessages);
        self::assertCount(7, $allMessages);
        self::assertEquals('Message 1 (en)', $allMessages['Message 1']);
    }

    public function testGetAllMessagesReturnsNullWhenGivenTextDomainIsNotFound(): void
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages('foo_domain');
        self::assertNull($allMessages);
    }

    public function testGetAllMessagesReturnsNullWhenGivenLocaleNotExist(): void
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages('default', 'es_ES');
        self::assertNull($allMessages);
    }

    public function testNullMessageArgumentShouldReturnAnEmptyString(): void
    {
        $loader             = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $pm                 = $this->translator->getPluginManager();
        $pm->configure([
            'services' => [
                'test' => $loader,
            ],
        ]);
        $this->translator->setPluginManager($pm);
        $this->translator->addTranslationFile('test', null);

        self::assertEquals('', $this->translator->translate(null));
    }

    public function testTranslateWithEmptyStringLocale(): void
    {
        $this->translator->setLocale('en_US');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/testarray/translation-more-en_US.php',
            'default',
            'en_US'
        );

        self::assertEquals('Message 8 (en)', $this->translator->translate('Message 8', 'default', ''));
    }
}
