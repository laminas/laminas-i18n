<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\PhpArray as PhpArrayLoader;
use Laminas\I18n\Translator\TextDomain;
use PHPUnit\Framework\TestCase;

use function get_include_path;
use function set_include_path;

final class PhpArrayTest extends TestCase
{
    private string $testFilesDir;
    private string $originalIncludePath;
    private string $pharFile;

    protected function setUp(): void
    {
        $this->testFilesDir        = __DIR__ . '/PhpArrayTest';
        $this->originalIncludePath = get_include_path();
        $this->pharFile            = __DIR__ . '/files/translations.phar';
    }

    protected function tearDown(): void
    {
        set_include_path($this->originalIncludePath);
    }

    public function testLoaderFailsToLoadMissingFile(): void
    {
        $loader = new PhpArrayLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find or open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderFailsToLoadNonArray(): void
    {
        $loader = new PhpArrayLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an array, but received');
        $loader->load('en_EN', $this->testFilesDir . '/failed.php');
    }

    public function testLoaderLoadsEmptyArray(): void
    {
        $loader     = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_empty.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);
    }

    public function testLoaderReturnsValidTextDomain(): void
    {
        $loader     = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals('Message 1 (en)', $textDomain['Message 1']);
        self::assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules(): void
    {
        $loader     = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        self::assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        self::assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        self::assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }

    public function testLoaderLoadsFromPharFileOnIncludePath(): void
    {
        set_include_path('phar://' . $this->pharFile);
        $loader     = new PhpArrayLoader(true);
        $textDomain = $loader->load('en_EN', 'translation_en.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals('Message 1 (en)', $textDomain['Message 1']);
        self::assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsFromIncludePath(): void
    {
        set_include_path($this->testFilesDir);
        $loader     = new PhpArrayLoader(true);
        $textDomain = $loader->load('en_EN', 'translation-de_DE.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals('Nachricht 1', $textDomain['Message 1']);
        self::assertEquals('Nachricht 8', $textDomain['Message 8']);
    }

    public function testLoaderLoadsFromPhar(): void
    {
        $loader     = new PhpArrayLoader(true);
        $textDomain = $loader->load('en_EN', 'phar://' . $this->pharFile . '/translation_en.php');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals('Message 1 (en)', $textDomain['Message 1']);
        self::assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }
}
