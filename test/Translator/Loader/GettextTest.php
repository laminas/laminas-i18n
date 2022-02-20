<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Translator\Loader\Gettext as GettextLoader;
use LaminasTest\I18n\TestCase;
use Locale;

use function get_include_path;
use function realpath;
use function set_include_path;

use const PATH_SEPARATOR;

class GettextTest extends TestCase
{
    protected $testFilesDir;
    protected $originalIncludePath;

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

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new GettextLoader();
        $this->expectException('Laminas\I18n\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Could not find or open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderFailsToLoadBadFile()
    {
        $loader = new GettextLoader();
        $this->expectException('Laminas\I18n\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('is not a valid gettext file');
        $loader->load('en_EN', $this->testFilesDir . '/failed.mo');
    }

    public function testLoaderLoadsEmptyFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load('en_EN', $this->testFilesDir . '/translation_empty.mo');
        $this->assertInstanceOf('Laminas\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderLoadsBigEndianFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load('en_EN', $this->testFilesDir . '/translation_bigendian.mo');
        $this->assertInstanceOf('Laminas\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new GettextLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader     = new GettextLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }

    public function testLoaderLoadsFromIncludePath()
    {
        $loader = new GettextLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsFromPhar()
    {
        $loader = new GettextLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'phar://' . $this->testFilesDir . '/translations.phar/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    /**
     * @group 6762
     * @group 6765
     */
    public function testLoaderLoadsPlural()
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
