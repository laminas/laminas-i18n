<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\IniFileReader;
use PHPUnit\Framework\TestCase;

final class IniFileReaderTest extends TestCase
{
    public function testInvalidFilePath(): void
    {
        $reader = new IniFileReader();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("/not-there.foo' doesn't exist or is not readable");
        $reader->read(__DIR__ . '/not-there.foo');
    }

    public function testInvalidFile(): void
    {
        $reader = new IniFileReader();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Error reading INI file: syntax error, unexpected '=' in");
        $reader->read(__DIR__ . '/IniFileReaderTest/invalid.ini');
    }

    public function testTypesCanBeParsed(): void
    {
        $reader = new IniFileReader(typed: true);
        $data   = $reader->read(__DIR__ . '/IniFileReaderTest/types.ini');

        self::assertSame([
            'production' => [
                'name'        => 'Bob Smith',
                'age'         => 55,
                'age_str'     => '55',
                'is_married'  => true,
                'is_employed' => false,
                'employer'    => null,
                'empty'       => '',
                'pi'          => 3.142,
            ],
        ], $data);
    }

    public function testSections(): void
    {
        $reader = new IniFileReader(processSections: true, typed: true);
        $data   = $reader->read(__DIR__ . '/IniFileReaderTest/sections.ini');

        self::assertSame([
            'production'           => [
                'env'            => 'production',
                'production_key' => 'foo',
            ],
            'staging : production' => [
                'env'         => 'staging',
                'staging_key' => 'bar',
            ],
        ], $data);
    }

    public function testSectionsWithoutProcessSections(): void
    {
        $reader = new IniFileReader(processSections: false, typed: true);
        $data   = $reader->read(__DIR__ . '/IniFileReaderTest/sections.ini');

        self::assertSame([
            'env'            => 'staging',
            'production_key' => 'foo',
            'staging_key'    => 'bar',
        ], $data);
    }

    public function testNestedSections(): void
    {
        $reader = new IniFileReader(processSections: true, typed: true);
        $data   = $reader->read(__DIR__ . '/IniFileReaderTest/nested-sections.ini');

        self::assertSame([
            'environments' => [
                'production' => [
                    'env'            => 'production',
                    'production_key' => 'foo',
                ],
                'staging'    => [
                    'env'         => 'staging',
                    'staging_key' => 'bar',
                ],
            ],
        ], $data);
    }

    public function testBasicString(): void
    {
        $ini = <<<'INI'
            test= "foo"
            bar[]= "baz"
            bar[]= "foo"
            
            INI;

        $reader = new IniFileReader();
        self::assertSame([
            'test' => 'foo',
            'bar'  => ['baz', 'foo'],
        ], $reader->readString($ini));
    }

    public function testFromStringWithSection(): void
    {
        $ini = <<<'INI'
            [all]
            test= "foo"
            bar[]= "baz"
            bar[]= "foo"
            
            INI;

        $reader = new IniFileReader();
        self::assertSame([
            'all' => [
                'test' => 'foo',
                'bar'  => ['baz', 'foo'],
            ],
        ], $reader->readString($ini));
    }

    public function testFromStringNested(): void
    {
        $ini = <<<'INI'
            bla.foo.bar = foobar
            bla.foobar[] = foobarArray
            bla.foo.baz[] = foobaz1
            bla.foo.baz[] = foobaz2
            
            INI;

        $reader = new IniFileReader();
        self::assertSame([
            'bla' => [
                'foo'    => [
                    'bar' => 'foobar',
                    'baz' => ['foobaz1', 'foobaz2'],
                ],
                'foobar' => ['foobarArray'],
            ],
        ], $reader->readString($ini));
    }

    public function testRepeatedSeparatorsInKeys(): void
    {
        $ini    = 'foo..baz=bar';
        $reader = new IniFileReader();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid key ".baz"');
        $reader->readString($ini);
    }
}
