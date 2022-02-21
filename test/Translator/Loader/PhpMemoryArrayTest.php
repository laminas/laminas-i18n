<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\PhpMemoryArray as PhpMemoryArrayLoader;
use Laminas\I18n\Translator\TextDomain;
use LaminasTest\I18n\TestCase;
use Locale;

use function realpath;

class PhpMemoryArrayTest extends TestCase
{
    private string $testFilesDir;

    protected function setUp(): void
    {
        parent::setUp();
        Locale::setDefault('en_US');

        $this->testFilesDir = realpath(__DIR__ . '/../_files/phpmemoryarray');
    }

    public function testLoaderFailsToLoadNonArray(): void
    {
        $loader = new PhpMemoryArrayLoader('foo');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an array, but received');
        $loader->load('en_US', 'default');
    }

    public function testLoaderFailsToLoadMissingTextDomain(): void
    {
        $loader = new PhpMemoryArrayLoader([]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected textdomain "default" to be an array, but it is not set');
        $loader->load('en_US', 'default');
    }

    public function testLoaderFailsToLoadNonArrayLocale(): void
    {
        $loader = new PhpMemoryArrayLoader(['default' => []]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected locale "en_US" to be an array, but it is not set');
        $loader->load('en_US', 'default');
    }

    public function testLoaderLoadsEmptyArray(): void
    {
        $loader     = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_empty.php');
        $textDomain = $loader->load('en_US', 'default');
        $this->assertInstanceOf(TextDomain::class, $textDomain);
    }

    public function testLoaderReturnsValidTextDomain(): void
    {
        $loader     = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules(): void
    {
        $loader     = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }
}
