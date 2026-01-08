<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TranslationCollector\FilePatternCollector;
use PHPUnit\Framework\TestCase;

final class FilePatternCollectorTest extends TestCase
{
    public function testAnEmptyCollectorWillYieldEmptyMessages(): void
    {
        $collector = new FilePatternCollector([], TestHelper::loaderManager());
        $messages  = $collector->collect('default', 'en_GB');
        self::assertCount(0, $messages);
    }

    public function testTheCorrectMessagesAreLoadedViaDefaultTextDomain(): void
    {
        $collector = TestHelper::filePatternCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/translations',
                'pattern'  => 'array-%s.php',
            ],
        ]);
        $english   = $collector->collect('default', 'en_GB');
        $german    = $collector->collect('default', 'de_DE');

        self::assertSame('Message (en)', $english['Message']);
        self::assertSame('Nachricht (de)', $german['Message']);
    }

    public function testMissingFilesAreIgnored(): void
    {
        $collector = TestHelper::filePatternCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/translations',
                'pattern'  => 'not-there-at-all-%s.php',
            ],
        ]);
        $messages  = $collector->collect('default', 'en_GB');
        self::assertNull($messages['Message']);
    }

    public function testInvalidLoaderAliasIsExceptional(): void
    {
        $collector = TestHelper::filePatternCollectorWithConfig([
            [
                'type'     => 'not-a-loader',
                'base_dir' => __DIR__ . '/translations',
                'pattern'  => 'array-%s.php',
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The specified loader "not-a-loader" is not a file loader');

        $collector->collect('default', 'en_GB');
    }

    public function testMultipleFilesInTheSameLocaleAreMergedSequentially(): void
    {
        $collector = TestHelper::filePatternCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/translations',
                'pattern'  => 'array-%s.php',
            ],
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/translations/more-translations',
                'pattern'  => 'array-%s.php',
            ],
        ]);
        $english   = $collector->collect('default', 'en_GB');

        self::assertSame('Another Message (en)', $english['Message']);
    }

    public function testTextDomainsInTheSameLocaleAreSegregated(): void
    {
        $collector = TestHelper::filePatternCollectorWithConfig([
            [
                'type'        => PhpArray::class,
                'base_dir'    => __DIR__ . '/translations',
                'pattern'     => 'array-%s.php',
                'text_domain' => 'foo',
            ],
            [
                'type'        => PhpArray::class,
                'base_dir'    => __DIR__ . '/translations/more-translations',
                'pattern'     => 'array-%s.php',
                'text_domain' => 'bar',
            ],
        ]);

        $foo = $collector->collect('foo', 'en_GB');
        $bar = $collector->collect('bar', 'en_GB');

        self::assertSame('Message (en)', $foo['Message']);
        self::assertSame('Another Message (en)', $bar['Message']);
    }
}
