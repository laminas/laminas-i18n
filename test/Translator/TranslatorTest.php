<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\EventManager\Event;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManager;
use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\Loader\Ini;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\Translator\TranslatorInterface;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use Locale;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
    private string $testFilesDir;
    private string $defaultLocale;

    protected function setUp(): void
    {
        $this->defaultLocale = Locale::getDefault();
        $this->testFilesDir  = __DIR__ . '/TranslatorTest';
    }

    protected function tearDown(): void
    {
        Locale::setDefault($this->defaultLocale);
    }

    private function translatorWithConfig(array $config): Translator
    {
        $container = TestHelper::containerWithConfig($config);
        $defaults  = $container->get(I18nDefaults::class);

        return new Translator(
            $container->get(TranslationCollectorInterface::class),
            $defaults->defaultLocale,
        );
    }

    public function testTranslationFromSeveralTranslationFiles(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'de_DE',
            'laminas-i18n' => [
                'translator' => [
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
                ],
            ],
        ]);

        //Test translations
        self::assertEquals(
            'Nachricht 1',
            $translator->translate('Message 1'),
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 9',
            $translator->translate('Message 9'),
        ); //translation-more-de_DE.php
        self::assertEquals(
            'Nachricht 10 - 0',
            $translator->translatePlural('Message 10', 'Message 10', 1),
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 10 - 1',
            $translator->translatePlural('Message 10', 'Message 10', 2),
        ); //translation-de_DE.php
        self::assertEquals(
            'Nachricht 11 - 0',
            $translator->translatePlural('Message 11', 'Message 11', 1),
        ); //translation-more-de_DE.php
        self::assertEquals(
            'Nachricht 11 - 1',
            $translator->translatePlural('Message 11', 'Message 11', 2),
        ); //translation-more-de_DE.php
    }

    public function testTranslationFromDifferentSourceTypes(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'de_DE',
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => [
                        [
                            'type'     => 'phparray',
                            'base_dir' => $this->testFilesDir . '/testarray',
                            'pattern'  => 'translation-%s.php',
                        ],
                    ],
                    'translation_files'         => [
                        [
                            'type'     => 'phparray',
                            'filename' => $this->testFilesDir . '/testarray/translation-more-de_DE.php',
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        self::assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
    }

    public function testDefaultLocale(): void
    {
        Locale::setDefault('en_FOO');
        $translator = $this->translatorWithConfig([
            'laminas-i18n' => [
                'defaultCountry' => 'US',
            ],
        ]);
        self::assertEquals('en_FOO', $translator->getLocale());
    }

    public function testForcedLocale(): void
    {
        $translator = $this->translatorWithConfig([]);
        $translator->setLocale('de_DE');
        self::assertEquals('de_DE', $translator->getLocale());
    }

    public function testTranslate(): void
    {
        $textDomain = new TextDomain(['foo' => 'bar']);
        $collector  = $this->createMock(TranslationCollectorInterface::class);
        $collector->expects($this->once())
            ->method('collect')
            ->with('default', 'en_US')
            ->willReturn($textDomain);

        $translator = new Translator($collector, 'en_US');

        self::assertEquals('bar', $translator->translate('foo'));
    }

    public function testTranslatePlurals(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/translation_en.php',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        $pl0 = $translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        self::assertEquals('Message 5 (en) Plural 0', $pl0);
        self::assertEquals('Message 5 (en) Plural 1', $pl1);
        self::assertEquals('Message 5 (en) Plural 2', $pl2);
    }

    public function testTranslatePluralsUsingIniFileFormat(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => Ini::class,
                            'filename' => $this->testFilesDir . '/translation_en.ini',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        $pl0 = $translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        self::assertEquals('Message 5 (en) Plural 0', $pl0);
        self::assertEquals('Message 5 (en) Plural 1', $pl1);
        self::assertEquals('Message 5 (en) Plural 2', $pl2);
    }

    public function testTranslatePluralsNonExistentLocale(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'es_ES',
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => [
                        [
                            'type'     => PhpArray::class,
                            'base_dir' => $this->testFilesDir . '/testarray',
                            'pattern'  => 'translation-%s.php',
                        ],
                    ],
                ],
            ],
        ]);

        $pl0 = $translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        self::assertEquals('Message 5', $pl0);
        self::assertEquals('Message 5 Plural', $pl1);
        self::assertEquals('Message 5 Plural', $pl2);
    }

    public function testTranslatePluralsNonExistentTranslation(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'de_DE',
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => [
                        [
                            'type'     => PhpArray::class,
                            'base_dir' => $this->testFilesDir . '/testarray',
                            'pattern'  => 'translation-%s.php',
                        ],
                    ],
                ],
            ],
        ]);

        $pl0 = $translator->translatePlural('Message 12', 'Message 12 Plural', 1);
        $pl1 = $translator->translatePlural('Message 12', 'Message 12 Plural', 2);
        $pl2 = $translator->translatePlural('Message 12', 'Message 12 Plural', 10);

        self::assertEquals('Message 12', $pl0);
        self::assertEquals('Message 12 Plural', $pl1);
        self::assertEquals('Message 12 Plural', $pl2);
    }

    public function testTranslateNoPlurals(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'ja_JP',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/testarray/translation-noplural-ja_JP.php',
                            'locale'   => 'ja_JP',
                        ],
                    ],
                ],
            ],
        ]);

        $pl0 = $translator->translatePlural('Message 9', 'Message 9 Plural', 1);
        $pl1 = $translator->translatePlural('Message 9', 'Message 9 Plural', 2);
        $pl2 = $translator->translatePlural('Message 9', 'Message 9 Plural', 10);

        self::assertEquals('Message 9 (ja)', $pl0);
        self::assertEquals('Message 9 (ja)', $pl1);
        self::assertEquals('Message 9 (ja)', $pl2);
    }

    public function testTranslateNonExistentLocale(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'es_ES',
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => [
                        [
                            'type'     => PhpArray::class,
                            'base_dir' => $this->testFilesDir . '/testarray',
                            'pattern'  => 'translation-%s.php',
                        ],
                    ],
                ],
            ],
        ]);

        // Test that a locale without translations does not cause warnings

        self::assertEquals('Message 1', $translator->translate('Message 1'));
        self::assertEquals('Message 9', $translator->translate('Message 9'));

        $translator->setLocale('fr_FR');

        self::assertEquals('Message 1', $translator->translate('Message 1'));
        self::assertEquals('Message 9', $translator->translate('Message 9'));
    }

    public function testTranslateNonExistentTranslation(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'de_DE',
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => [
                        [
                            'type'     => PhpArray::class,
                            'base_dir' => $this->testFilesDir . '/testarray',
                            'pattern'  => 'translation-%s.php',
                        ],
                    ],
                ],
            ],
        ]);

        // Test that a locale without translations does not cause warnings

        self::assertEquals('Message 13', $translator->translate('Message 13'));
    }

    public function testEnableDisableEventManger(): void
    {
        $translator = $this->translatorWithConfig([]);

        self::assertFalse($translator->isEventManagerEnabled(), 'Default value');

        $translator->enableEventManager();
        self::assertTrue($translator->isEventManagerEnabled());

        $translator->disableEventManager();
        self::assertFalse($translator->isEventManagerEnabled());
    }

    public function testMissingTranslationEvent(): void
    {
        $translator  = $this->translatorWithConfig([]);
        $actualEvent = null;

        $translator->enableEventManager();
        $translator->getEventManager()->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            },
        );

        $translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(Event::class, $actualEvent);
        self::assertEquals(
            [
                'message'     => 'foo',
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ],
            $actualEvent->getParams(),
        );

        // But fire no event when disabled
        $actualEvent = null;
        $translator->disableEventManager();
        $translator->translate('foo', 'bar', 'baz');
        self::assertNull($actualEvent);
    }

    public function testUsersCanSupplyASpecificEventManagerInstance(): void
    {
        $actualEvent  = null;
        $eventManager = new EventManager();
        $eventManager->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            },
        );

        $container = TestHelper::containerWithConfig([]);

        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_GB',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $eventManager,
        );

        self::assertSame($eventManager, $translator->getEventManager());
        self::assertTrue($translator->isEventManagerEnabled());

        $translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(Event::class, $actualEvent);
        self::assertEquals(
            [
                'message'     => 'foo',
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ],
            $actualEvent->getParams(),
        );
    }

    public function testListenerOnMissingTranslationEventCanReturnString(): void
    {
        $trigger      = null;
        $doNotTrigger = null;
        $translator   = $this->translatorWithConfig([]);

        $translator->enableEventManager();
        $events = $translator->getEventManager();
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function () use (&$trigger) {
                $trigger = true;
            },
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static fn() => 'EVENT TRIGGERED',
        );
        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            static function () use (&$doNotTrigger) {
                $doNotTrigger = true;
            },
        );

        $result = $translator->translate('foo', 'bar', 'baz');
        self::assertTrue($trigger);
        self::assertEquals('EVENT TRIGGERED', $result);
        self::assertNull($doNotTrigger);
    }

    public function testNoMessagesLoadedEvent(): void
    {
        $actualEvent = null;
        $translator  = $this->translatorWithConfig([]);

        $translator->enableEventManager();
        $translator
            ->getEventManager()
            ->attach(Translator::EVENT_NO_MESSAGES_LOADED, function (EventInterface $event) use (&$actualEvent) {
                $actualEvent = $event;
            });

        $translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(Event::class, $actualEvent);
        self::assertEquals(
            [
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ],
            $actualEvent->getParams(),
        );

        // But fire no event when disabled
        $actualEvent = null;
        $translator->disableEventManager();
        $translator->translate('foo', 'bar', 'baz');
        self::assertNull($actualEvent);
    }

    public function testListenerOnNoMessagesLoadedEventCanReturnTextDomainObject(): void
    {
        $trigger      = null;
        $doNotTrigger = null;
        $translator   = $this->translatorWithConfig([]);
        $textDomain   = new TextDomain([
            'foo' => 'BOOYAH',
        ]);

        $translator->enableEventManager();
        $events = $translator->getEventManager();
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static function () use (&$trigger): void {
                $trigger = true;
            },
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static fn() => $textDomain,
        );
        $events->attach(
            Translator::EVENT_NO_MESSAGES_LOADED,
            static function () use (&$doNotTrigger) {
                $doNotTrigger = true;
            },
        );

        $result = $translator->translate('foo', 'bar', 'baz');

        self::assertTrue($trigger);
        self::assertNull($doNotTrigger);
        self::assertEquals('BOOYAH', $result);
    }

    public function testGetAllMessagesLoadedInTranslator(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/translation_en.php',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        $allMessages = $translator->getAllMessages();
        self::assertInstanceOf(TextDomain::class, $allMessages);
        self::assertCount(7, $allMessages);
        self::assertEquals('Message 1 (en)', $allMessages['Message 1']);
    }

    public function testGetAllMessagesReturnsEmptySetWhenGivenTextDomainIsNotFound(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/translation_en.php',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        $allMessages = $translator->getAllMessages('foo_domain');
        self::assertInstanceOf(TextDomain::class, $allMessages);
        self::assertCount(0, $allMessages);
    }

    public function testGetAllMessagesReturnsNullWhenGivenLocaleNotExist(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/translation_en.php',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        $allMessages = $translator->getAllMessages('default', 'es_ES');
        self::assertInstanceOf(TextDomain::class, $allMessages);
        self::assertCount(0, $allMessages);
    }

    public function testTranslateWithEmptyStringLocale(): void
    {
        $translator = $this->translatorWithConfig([
            'locale'       => 'en_US',
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => [
                        [
                            'type'     => PhpArray::class,
                            'filename' => $this->testFilesDir . '/testarray/translation-more-en_US.php',
                            'locale'   => 'en_US',
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals('Message 8 (en)', $translator->translate('Message 8'));
    }

    public function testTheDefaultTextDomainIsUsedAsConfiguredInTranslate(): void
    {
        $collector = $this->createMock(TranslationCollectorInterface::class);
        $collector->expects($this->once())
            ->method('collect')
            ->with('kermit', 'en_GB')
            ->willReturn(new TextDomain(['foo' => 'bar']));

        $translator = new Translator($collector, 'en_GB', null, 'kermit');
        self::assertSame('bar', $translator->translate('foo'));
    }

    public function testTheGlobalDefaultTextDomainIsUsedWhenTextDomainIsNotSpecified(): void
    {
        $collector = $this->createMock(TranslationCollectorInterface::class);
        $collector->expects($this->once())
            ->method('collect')
            ->with(TranslatorInterface::DEFAULT_TEXT_DOMAIN, 'en_GB')
            ->willReturn(new TextDomain(['foo' => 'bar']));

        $translator = new Translator($collector, 'en_GB');
        self::assertSame('bar', $translator->translate('foo'));
    }
}
