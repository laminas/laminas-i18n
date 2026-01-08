<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TranslationCollector\FileListCollector;
use PHPUnit\Framework\TestCase;

final class FileListCollectorTest extends TestCase
{
    public function testAnEmptyCollectorWillYieldEmptyMessages(): void
    {
        $collector = new FileListCollector([], TestHelper::loaderManager());
        $messages  = $collector->collect('default', 'en_GB');
        self::assertCount(0, $messages);
    }

    public function testTheCorrectMessagesAreLoadedViaDefaultTextDomain(): void
    {
        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-de_DE.php',
                'locale'   => 'de_DE',
            ],
        ]);
        $english   = $collector->collect('default', 'en_GB');
        $german    = $collector->collect('default', 'de_DE');

        self::assertSame('Message (en)', $english['Message']);
        self::assertSame('Nachricht (de)', $german['Message']);
    }

    public function testInvalidLoaderSpecificationWillCauseAnExceptionDuringCollection(): void
    {
        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => 'goats',
                'filename' => __DIR__ . '/translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
        ]);
        // Collection for a different (empty) locale will not cause an exception
        $collector->collect('default', 'fr_FR');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The specified loader "goats" is not a known file loader or alias');
        $collector->collect('default', 'en_GB');
    }

    public function testInvalidFilePathCausesUpstreamException(): void
    {
        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'filename' => 'file-does-not-exist',
                'locale'   => 'en_GB',
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find or open file file-does-not-exist');

        $collector->collect('default', 'en_GB');
    }

    public function testMultipleFilesInTheSameLocaleAreMergedSequentially(): void
    {
        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-de_DE.php',
                'locale'   => 'en_GB',
            ],
        ]);
        $messages  = $collector->collect('default', 'en_GB');

        self::assertSame('Nachricht (de)', $messages['Message']);

        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-de_DE.php',
                'locale'   => 'en_GB',
            ],
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
        ]);
        $messages  = $collector->collect('default', 'en_GB');

        self::assertSame('Message (en)', $messages['Message']);
    }

    public function testTextDomainsInTheSameLocaleAreSegregated(): void
    {
        $collector = TestHelper::fileListCollectorWithConfig([
            [
                'type'        => PhpArray::class,
                'filename'    => __DIR__ . '/translations/array-en_GB.php',
                'locale'      => 'en_GB',
                'text_domain' => 'foo',
            ],
            [
                'type'        => PhpArray::class,
                'filename'    => __DIR__ . '/translations/array-de_DE.php',
                'locale'      => 'en_GB',
                'text_domain' => 'bar',
            ],
        ]);

        $foo = $collector->collect('foo', 'en_GB');
        $bar = $collector->collect('bar', 'en_GB');

        self::assertSame('Message (en)', $foo['Message']);
        self::assertSame('Nachricht (de)', $bar['Message']);
    }
}
