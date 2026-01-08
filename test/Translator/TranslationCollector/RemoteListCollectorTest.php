<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\PhpMemoryArray;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TranslationCollector\RemoteListCollector;
use PHPUnit\Framework\TestCase;

final class RemoteListCollectorTest extends TestCase
{
    public function testEmptyCollectorYieldsEmptyMessages(): void
    {
        $collector = new RemoteListCollector([], TestHelper::loaderManager());
        $messages  = $collector->collect('default', 'en_GB');
        self::assertNull($messages['Message']);
        self::assertCount(0, $messages);
    }

    public function testInvalidLoaderTypeCausesException(): void
    {
        $collector = new RemoteListCollector(['default' => ['foo']], TestHelper::loaderManager());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The specified loader "foo" is not a remote loader');

        $collector->collect('default', 'en_GB');
    }

    public function testMessagesAreCollected(): void
    {
        $loader = new PhpMemoryArray([
            'default' => [
                'en_GB' => [
                    'Message' => 'Translation',
                ],
            ],
        ]);

        $container = TestHelper::containerWithConfig([
            'translator_plugins' => [
                'services' => [
                    'SomeLoader' => $loader,
                ],
            ],
        ]);

        $collector = new RemoteListCollector(
            ['default' => ['SomeLoader']],
            $container->get(MessageLoaderPluginManagerInterface::class),
        );

        $messages = $collector->collect('default', 'en_GB');
        self::assertSame('Translation', $messages['Message']);
    }

    public function testMessagesAreMergedSequentially(): void
    {
        $loader1 = new PhpMemoryArray([
            'default' => [
                'en_GB' => [
                    'Message' => 'Translation 1',
                ],
            ],
        ]);

        $loader2 = new PhpMemoryArray([
            'default' => [
                'en_GB' => [
                    'Message' => 'Translation 2',
                ],
            ],
        ]);

        $container = TestHelper::containerWithConfig([
            'translator_plugins' => [
                'services' => [
                    'Loader1' => $loader1,
                    'Loader2' => $loader2,
                ],
            ],
        ]);

        $collector = new RemoteListCollector(
            ['default' => ['Loader1', 'Loader2']],
            $container->get(MessageLoaderPluginManagerInterface::class),
        );

        $messages = $collector->collect('default', 'en_GB');
        self::assertSame('Translation 2', $messages['Message']);

        $collector = new RemoteListCollector(
            ['default' => ['Loader2', 'Loader1']],
            $container->get(MessageLoaderPluginManagerInterface::class),
        );

        $messages = $collector->collect('default', 'en_GB');
        self::assertSame('Translation 1', $messages['Message']);
    }

    public function testMessagesAreEmptyWhenTheLoaderHasNone(): void
    {
        $loader = $this->createMock(RemoteLoaderInterface::class);
        $loader->expects($this->once())
            ->method('load')
            ->with('en_GB', 'default')
            ->willReturn(null);

        $container = TestHelper::containerWithConfig([
            'translator_plugins' => [
                'services' => [
                    'SomeLoader' => $loader,
                ],
            ],
        ]);

        $collector = new RemoteListCollector(
            ['default' => ['SomeLoader']],
            $container->get(MessageLoaderPluginManagerInterface::class),
        );

        $messages = $collector->collect('default', 'en_GB');
        self::assertNull($messages['Message']);
    }

    public function testMessagesAreEmptyWhenNoTextDomainMatchesConfiguredLoaders(): void
    {
        $loader = $this->createMock(RemoteLoaderInterface::class);
        $loader->expects($this->never())
            ->method('load');

        $container = TestHelper::containerWithConfig([
            'translator_plugins' => [
                'services' => [
                    'SomeLoader' => $loader,
                ],
            ],
        ]);

        $collector = new RemoteListCollector(
            ['default' => ['SomeLoader']],
            $container->get(MessageLoaderPluginManagerInterface::class),
        );

        $messages = $collector->collect('other-text-domain', 'en_GB');
        self::assertNull($messages['Message']);
    }
}
