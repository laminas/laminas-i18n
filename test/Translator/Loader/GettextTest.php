<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Translator\Loader\Gettext as GettextLoader;
use Locale;
use PHPUnit_Framework_TestCase as TestCase;

class GettextTest extends TestCase
{
    protected $testFilesDir;
    protected $originalLocale;

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->originalLocale = Locale::getDefault();
        Locale::setDefault('en_EN');

        $this->testFilesDir = realpath(__DIR__ . '/../_files');
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->originalLocale);
        }
    }

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new GettextLoader();
        $this->setExpectedException('Laminas\I18n\Exception\InvalidArgumentException', 'Could not open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderFailsToLoadBadFile()
    {
        $loader = new GettextLoader();
        $this->setExpectedException('Laminas\I18n\Exception\InvalidArgumentException',
                                    'is not a valid gettext file');
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
}
