<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Laminas\Cache\StorageFactory as CacheFactory;
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\EventManager\EventInterface;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\Config;
use LaminasTest\I18n\Translator\TestAsset\Loader as TestLoader;
use Locale;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $originalLocale;

    /**
     * @var string
     */
    protected $testFilesDir;

    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->originalLocale = Locale::getDefault();
        $this->translator     = new Translator();

        Locale::setDefault('en_EN');

        $this->testFilesDir = __DIR__ . '/_files';
    }

    protected function tearDown(): void
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->originalLocale);
        }
    }

    public function testFactoryCreatesTranslator()
    {
        $translator = Translator::factory([
            'locale' => 'de_DE',
            'patterns' => [
                [
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                ]
            ],
            'files' => [
                [
                    'type' => 'phparray',
                    'filename' => $this->testFilesDir . '/translation_en.php',
                ]
            ]
        ]);

        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
        $this->assertEquals('de_DE', $translator->getLocale());
    }

    public function testTranslationFromSeveralTranslationFiles()
    {
        $translator = Translator::factory([
            'locale' => 'de_DE',
            'translation_file_patterns' => [
                [
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                ],
                [
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-more-%s.php'
                ]
            ]
        ]);

        //Test translator instance
        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);

        //Test translations
        $this->assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        $this->assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
        $this->assertEquals('Nachricht 10 - 0', $translator->translatePlural('Message 10', 'Message 10', 1)); //translation-de_DE.php
        $this->assertEquals('Nachricht 10 - 1', $translator->translatePlural('Message 10', 'Message 10', 2)); //translation-de_DE.php
        $this->assertEquals('Nachricht 11 - 0', $translator->translatePlural('Message 11', 'Message 11', 1)); //translation-more-de_DE.php
        $this->assertEquals('Nachricht 11 - 1', $translator->translatePlural('Message 11', 'Message 11', 2)); //translation-more-de_DE.php
    }

    public function testTranslationFromDifferentSourceTypes()
    {
        $translator = Translator::factory([
            'locale' => 'de_DE',
            'translation_file_patterns' => [
                [
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-de_DE.php'
                ],
            ],
            'translation_files' => [
                [
                    'type'     => 'phparray',
                    'filename' => $this->testFilesDir . '/testarray/translation-more-de_DE.php'
                ]
            ]
        ]);

        $this->assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        $this->assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
    }

    public function testFactoryCreatesTranslatorWithCache()
    {
        $cache = new SimpleCacheDecorator(CacheFactory::factory(['adapter' => 'memory']));

        $translator = Translator::factory([
            'locale' => 'de_DE',
            'patterns' => [
                [
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                ]
            ],
            'cache' => $cache
        ]);

        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
        $this->assertEquals($cache, $translator->getCache());
    }

    public function testDefaultLocale()
    {
        $this->assertEquals('en_EN', $this->translator->getLocale());
    }

    public function testForcedLocale()
    {
        $this->translator->setLocale('de_DE');
        $this->assertEquals('de_DE', $this->translator->getLocale());
    }

    public function testTranslate()
    {
        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $config = new Config([
            'services' => [
                'test' => $loader
            ]
        ]);
        $pm = $this->translator->getPluginManager();
        $config->configureServiceManager($pm);
        $this->translator->setPluginManager($pm);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsLoadedFromCache()
    {
        $cache = new SimpleCacheDecorator(CacheFactory::factory(['adapter' => 'memory']));
        $this->translator->setCache($cache);

        $cache->set(
            $this->translator->getCacheId('default', 'en_EN'),
            new TextDomain(['foo' => 'bar'])
        );

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsAreStoredInCache()
    {
        $cache = new SimpleCacheDecorator(CacheFactory::factory(['adapter' => 'memory']));
        $this->translator->setCache($cache);

        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $config = new Config(['services' => ['test' => $loader]]);
        $plugins = $this->translator->getPluginManager();
        $config->configureServiceManager($plugins);
        $this->translator->setPluginManager($plugins);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));

        $item = $cache->get($this->translator->getCacheId('default', 'en_EN'));
        $this->assertInstanceOf('Laminas\I18n\Translator\TextDomain', $item);
        $this->assertEquals('bar', $item['foo']);
    }

    public function testTranslationsAreClearedFromCache()
    {
        $textDomain = 'default';
        $locale     = 'en_EN';

        $cache = new SimpleCacheDecorator(CacheFactory::factory(['adapter' => 'memory']));
        $this->translator->setCache($cache);

        $cache->set(
            $this->translator->getCacheId($textDomain, $locale),
            new TextDomain(['foo' => 'bar'])
        );

        $this->assertTrue($this->translator->clearCache($textDomain, $locale));

        $item = $cache->get($this->translator->getCacheId($textDomain, $locale));
        $this->assertNull($item);
    }

    public function testClearCacheReturnsFalseIfNoCacheIsPresent()
    {
        $this->assertFalse($this->translator->clearCache('default', 'en_EN'));
    }

    public function testTranslatePlurals()
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

        $this->assertEquals('Message 5 (en) Plural 0', $pl0);
        $this->assertEquals('Message 5 (en) Plural 1', $pl1);
        $this->assertEquals('Message 5 (en) Plural 2', $pl2);
    }

    public function testTranslatePluralsNonExistantLocale()
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

        $this->assertEquals('Message 5', $pl0);
        $this->assertEquals('Message 5 Plural', $pl1);
        $this->assertEquals('Message 5 Plural', $pl2);
    }

    public function testTranslatePluralsNonExistantTranslation()
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

        $this->assertEquals('Message 12', $pl0);
        $this->assertEquals('Message 12 Plural', $pl1);
        $this->assertEquals('Message 12 Plural', $pl2);
    }

    public function testTranslateNoPlurals()
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

        $this->assertEquals('Message 9 (ja)', $pl0);
        $this->assertEquals('Message 9 (ja)', $pl1);
        $this->assertEquals('Message 9 (ja)', $pl2);
    }

    public function testTranslateNonExistantLocale()
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        // Test that a locale without translations does not cause warnings

        $this->translator->setLocale('es_ES');

        $this->assertEquals('Message 1', $this->translator->translate('Message 1'));
        $this->assertEquals('Message 9', $this->translator->translate('Message 9'));

        $this->translator->setLocale('fr_FR');

        $this->assertEquals('Message 1', $this->translator->translate('Message 1'));
        $this->assertEquals('Message 9', $this->translator->translate('Message 9'));
    }

    public function testTranslateNonExistantTranslation()
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        // Test that a locale without translations does not cause warnings

        $this->translator->setLocale('de_DE');

        $this->assertEquals('Message 13', $this->translator->translate('Message 13'));
    }

    public function testEnableDisableEventManger()
    {
        $this->assertFalse($this->translator->isEventManagerEnabled(), 'Default value');

        $this->translator->enableEventManager();
        $this->assertTrue($this->translator->isEventManagerEnabled());

        $this->translator->disableEventManager();
        $this->assertFalse($this->translator->isEventManagerEnabled());
    }

    public function testEnableEventMangerViaFactory()
    {
        $translator = Translator::factory([
            'event_manager_enabled' => true
        ]);
        $this->assertTrue($translator->isEventManagerEnabled());

        $translator = Translator::factory([]);
        $this->assertFalse($translator->isEventManagerEnabled());
    }

    public function testMissingTranslationEvent()
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

        $this->assertInstanceOf('Laminas\EventManager\Event', $actualEvent);
        $this->assertEquals(
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
        $this->assertNull($actualEvent);
    }

    public function testListenerOnMissingTranslationEventCanReturnString()
    {
        $trigger     = null;
        $doNotTriger = null;

        $this->translator->enableEventManager();
        $events = $this->translator->getEventManager();
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use (&$trigger) {
                $trigger = true;
            }
            // @codingStandardsIgnoreEnd
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) {
                return 'EVENT TRIGGERED';
            }
            // @codingStandardsIgnoreEnd
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use (&$doNotTrigger) {
                $doNotTrigger = true;
            }
            // @codingStandardsIgnoreEnd
        );

        $result = $this->translator->translate('foo', 'bar', 'baz');
        $this->assertTrue($trigger);
        $this->assertEquals('EVENT TRIGGERED', $result);
        $this->assertNull($doNotTrigger);
    }

    public function testNoMessagesLoadedEvent()
    {
        $actualEvent = null;

        $this->translator->enableEventManager();
        $this->translator
            ->getEventManager()
            ->attach(Translator::EVENT_NO_MESSAGES_LOADED, function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            });

        $this->translator->translate('foo', 'bar', 'baz');

        $this->assertInstanceOf('Laminas\EventManager\Event', $actualEvent);
        $this->assertEquals(
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
        $this->assertNull($actualEvent);
    }

    public function testListenerOnNoMessagesLoadedEventCanReturnTextDomainObject()
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
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use (&$trigger) {
                $trigger = true;
            }
            // @codingStandardsIgnoreEnd
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use ($textDomain) {
                return $textDomain;
            }
            // @codingStandardsIgnoreEnd
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            // @codingStandardsIgnoreStart Generic.WhiteSpace.ScopeIndent.IncorrectExact
            static function (EventInterface $event) use (&$doNotTrigger) {
                $doNotTrigger = true;
            }
            // @codingStandardsIgnoreEnd
        );

        $result = $this->translator->translate('foo', 'bar', 'baz');

        $this->assertTrue($trigger);
        $this->assertNull($doNotTrigger);
        $this->assertEquals('BOOYAH', $result);
    }

    public function testGetAllMessagesLoadedInTranslator()
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages();
        $this->assertInstanceOf(TextDomain::class, $allMessages);
        $this->assertCount(7, $allMessages);
        $this->assertEquals('Message 1 (en)', $allMessages['Message 1']);
    }

    public function testGetAllMessagesReturnsNullWhenGivenTextDomainIsNotFound()
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages('foo_domain');
        $this->assertNull($allMessages);
    }

    public function testGetAllMessagesReturnsNullWhenGivenLocaleNotExist()
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $allMessages = $this->translator->getAllMessages('default', 'es_ES');
        $this->assertNull($allMessages);
    }

    /**
     * @group 33
     */
    public function testNullMessageArgumentShouldReturnAnEmptyString()
    {
        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(['foo' => 'bar']);
        $config = new Config(['services' => [
            'test' => $loader
        ]]);
        $pm = $this->translator->getPluginManager();
        $config->configureServiceManager($pm);
        $this->translator->setPluginManager($pm);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('', $this->translator->translate(null));
    }
}
