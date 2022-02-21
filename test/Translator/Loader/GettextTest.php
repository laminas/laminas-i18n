<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\Gettext as GettextLoader;
use Laminas\I18n\Translator\TextDomain;
use LaminasTest\I18n\TestCase;
use Locale;

use function get_include_path;
use function realpath;
use function set_include_path;

use const PATH_SEPARATOR;

class GettextTest extends TestCase
{
    private string $testFilesDir;
    private string $originalIncludePath;

    protected function setUp(): void
    {
        parent::setUp();
        Locale::setDefault('en_EN');

        $this->testFilesDir = realpath(__DIR__ . '/../_files');

        $this->originalIncludePath = get_include_path();
        set_include_path($this->testFilesDir . PATH_SEPARATOR . $this->testFilesDir . '/translations.phar');
    }

    protected function tearDown(): void
    {
        set_include_path($this->originalIncludePath);
        parent::tearDown();
    }

    public function testLoaderFailsToLoadMissingFile(): void
    {
        $loader = new GettextLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find or open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderFailsToLoadBadFile(): void
    {
        $loader = new GettextLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid gettext file');
        $loader->load('en_EN', $this->testFilesDir . '/failed.mo');
    }

    public function testLoaderLoadsEmptyFile(): void
    {
        $loader = new GettextLoader();
        $domain = $loader->load('en_EN', $this->testFilesDir . '/translation_empty.mo');
        $this->assertInstanceOf(TextDomain::class, $domain);
    }

    public function testLoaderLoadsBigEndianFile(): void
    {
        $loader = new GettextLoader();
        $domain = $loader->load('en_EN', $this->testFilesDir . '/translation_bigendian.mo');
        $this->assertInstanceOf(TextDomain::class, $domain);
    }

    public function testLoaderReturnsValidTextDomain(): void
    {
        $loader     = new GettextLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules(): void
    {
        $loader     = new GettextLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }

    public function testLoaderLoadsFromIncludePath(): void
    {
        $loader = new GettextLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsFromPhar(): void
    {
        $loader = new GettextLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'phar://' . $this->testFilesDir . '/translations.phar/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPlural(): void
    {
        $loader = new GettextLoader();

        $loader->setUseIncludePath(true);

        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals(
            [
                'Message A (en) Plural 0',
                'Message A (en) Plural 1',
                'Message A (en) Plural 2',
            ],
            $textDomain['Message A']
        );

        $this->assertEquals(
            [
                'Message B (en) Plural 0',
                'Message B (en) Plural 1',
                'Message B (en) Plural 2',
            ],
            $textDomain['Message B']
        );
    }
}
