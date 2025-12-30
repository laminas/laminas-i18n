<?php

declare(strict_types=1);

namespace LaminasTest\i18n\Translator\Value;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Value\TranslationFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class TranslationFileTest extends TestCase
{
    /**
     * @return list<array{
     *     0: mixed,
     *     1: mixed,
     *     2: mixed,
     *     3: mixed,
     *     4: string,
     * }>
     */
    public static function invalidParamsProvider(): array
    {
        return [
            [null, 'some-file.mo', 'en_GB', 'domain', 'type'],
            ['', 'some-file.mo', 'en_GB', 'domain', 'type'],
            [1, 'some-file.mo', 'en_GB', 'domain', 'type'],
            ['foo', null, 'en_GB', 'domain', 'filename'],
            ['foo', '', 'en_GB', 'domain', 'filename'],
            ['foo', 1, 'en_GB', 'domain', 'filename'],
            ['foo', 'some-file.mo', [], 'domain', 'locale'],
            ['foo', 'some-file.mo', '', 'domain', 'locale'],
            ['foo', 'some-file.mo', 1, 'domain', 'locale'],
            ['foo', 'some-file.mo', 'en_GB', [], 'text_domain'],
            ['foo', 'some-file.mo', 'en_GB', '', 'text_domain'],
            ['foo', 'some-file.mo', 'en_GB', 1, 'text_domain'],
        ];
    }

    #[DataProvider('invalidParamsProvider')]
    public function testAllValuesMustBeNonEmptyString(
        mixed $type,
        mixed $filename,
        mixed $locale,
        mixed $textDomain,
        string $invalidKey,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The key "%s" must be set and should contain a non-empty string',
            $invalidKey,
        ));

        TranslationFile::fromArray([
            'type'        => $type,
            'filename'    => $filename,
            'locale'      => $locale,
            'text_domain' => $textDomain,
        ], 'foo', 'bar');
    }

    public function testDefaultsAreUsedForMissingKeys(): void
    {
        $file = TranslationFile::fromArray([
            'type'     => 'foo',
            'filename' => 'bar',
        ], 'baz', 'bat');

        self::assertSame('foo', $file->type);
        self::assertSame('bar', $file->filename);
        self::assertSame('baz', $file->locale);
        self::assertSame('bat', $file->textDomain);

        $file = TranslationFile::fromArray([
            'type'     => 'foo',
            'filename' => 'bar',
            'locale'   => 'en',
        ], 'baz', 'bat');

        self::assertSame('foo', $file->type);
        self::assertSame('bar', $file->filename);
        self::assertSame('en', $file->locale);
        self::assertSame('bat', $file->textDomain);

        $file = TranslationFile::fromArray([
            'type'        => 'foo',
            'filename'    => 'bar',
            'text_domain' => 'bing',
        ], 'baz', 'bat');

        self::assertSame('foo', $file->type);
        self::assertSame('bar', $file->filename);
        self::assertSame('baz', $file->locale);
        self::assertSame('bing', $file->textDomain);
    }
}
