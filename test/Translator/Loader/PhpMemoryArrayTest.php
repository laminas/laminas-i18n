<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\PhpMemoryArray as PhpMemoryArrayLoader;
use Laminas\I18n\Translator\TextDomain;
use PHPUnit\Framework\TestCase;

final class PhpMemoryArrayTest extends TestCase
{
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
        $loader     = new PhpMemoryArrayLoader(include __DIR__ . '/PhpMemoryArrayTest/translation_empty.php');
        $textDomain = $loader->load('en_US', 'default');
        self::assertInstanceOf(TextDomain::class, $textDomain);
    }

    public function testLoaderReturnsValidTextDomain(): void
    {
        $loader     = new PhpMemoryArrayLoader(include __DIR__ . '/PhpMemoryArrayTest/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals('Message 1 (en)', $textDomain['Message 1']);
        self::assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules(): void
    {
        $loader     = new PhpMemoryArrayLoader(include __DIR__ . '/PhpMemoryArrayTest/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');
        self::assertInstanceOf(TextDomain::class, $textDomain);

        self::assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        self::assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        self::assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        self::assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }
}
