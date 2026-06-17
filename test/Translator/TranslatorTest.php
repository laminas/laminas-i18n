<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\Event\MissingTranslationEvent;
use Laminas\I18n\Translator\Event\NoMessagesLoadedEvent;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\Translator\TranslatorInterface;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use Locale;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

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

    public function testMissingTranslationEventDispatchesWhenDispatcherIsPresent(): void
    {
        $actualEvent = null;
        $dispatcher  = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (object $event) use (&$actualEvent) {
                if ($event instanceof MissingTranslationEvent) {
                    $actualEvent = $event;
                }
                return $event;
            });

        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher,
        );

        $translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(MissingTranslationEvent::class, $actualEvent);
        self::assertEquals('foo', $actualEvent->getMessage());
        self::assertEquals('baz', $actualEvent->getLocale());
        self::assertEquals('bar', $actualEvent->getTextDomain());
    }

    public function testMissingTranslationEventIsIgnoredWhenDispatcherIsAbsent(): void
    {
        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            null // Explicitly passing null to check non-event behavior
        );

        // Assert that the translation execution completes gracefully without a dispatcher
        $result = $translator->translate('foo', 'bar', 'baz');
        self::assertEquals('foo', $result);
    }

    public function testUsersCanSupplyASpecificEventDispatcherInstance(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $container  = TestHelper::containerWithConfig([]);

        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_GB',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher,
        );

        self::assertSame($dispatcher, $translator->getEventDispatcher());
    }

    public function testListenerOnMissingTranslationEventCanReturnString(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (object $event) {
                if ($event instanceof MissingTranslationEvent) {
                    $event->setTranslation('EVENT TRIGGERED');
                }
                return $event;
            });

        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher,
        );

        $result = $translator->translate('foo', 'bar', 'baz');
        self::assertEquals('EVENT TRIGGERED', $result);
    }

    public function testNoMessagesLoadedEventDispatchesWhenDispatcherIsPresent(): void
    {
        $actualEvent = null;
        $dispatcher  = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (object $event) use (&$actualEvent) {
                if ($event instanceof NoMessagesLoadedEvent) {
                    $actualEvent = $event;
                }
                return $event;
            });

        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher,
        );

        $translator->translate('foo', 'bar', 'baz');

        self::assertInstanceOf(NoMessagesLoadedEvent::class, $actualEvent);
        self::assertEquals('baz', $actualEvent->getLocale());
        self::assertEquals('bar', $actualEvent->getTextDomain());
    }

    public function testNoMessagesLoadedEventIsIgnoredWhenDispatcherIsAbsent(): void
    {
        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            null
        );

        $result = $translator->translate('foo', 'bar', 'baz');
        self::assertEquals('foo', $result);
    }

    public function testListenerOnNoMessagesLoadedEventCanReturnTextDomainObject(): void
    {
        $textDomain = new TextDomain([
            'foo' => 'BOOYAH',
        ]);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (object $event) use ($textDomain) {
                if ($event instanceof NoMessagesLoadedEvent) {
                    $event->setMessages($textDomain);
                }
                return $event;
            });

        $container  = TestHelper::containerWithConfig([]);
        $translator = new Translator(
            $container->get(TranslationCollectorInterface::class),
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher,
        );

        $result = $translator->translate('foo', 'bar', 'baz');

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

    public function testMissingTranslationEventShortCircuitsWhenTranslationIsProvided(): void
    {
        $listenerExecution = [
            'first'  => false,
            'second' => false,
        ];

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')
            ->willReturnCallback(function (object $event) use (&$listenerExecution) {
                if ($event instanceof MissingTranslationEvent) {
                    $listenerExecution['first'] = true;
                    $event->setTranslation('Found by first listener!');

                    if ($event->isPropagationStopped()) {
                        return $event;
                    }

                    $listenerExecution['second'] = true;
                    $event->setTranslation('Overwritten by second listener!');
                }

                return $event;
            });

        $collector = $this->createMock(TranslationCollectorInterface::class);
        $collector->method('collect')->willReturn(new TextDomain());

        $translator = new Translator(
            $collector,
            'en_US',
            null,
            TranslatorInterface::DEFAULT_TEXT_DOMAIN,
            $dispatcher
        );

        $result = $translator->translate('some-missing-key', 'default', 'en_US');

        self::assertSame('Found by first listener!', $result);
        self::assertTrue($listenerExecution['first'], 'First listener should have executed.');
        self::assertFalse($listenerExecution['second'], 'Second listener should have been skipped entirely.');
    }
}
