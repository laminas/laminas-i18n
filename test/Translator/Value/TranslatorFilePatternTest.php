<?php

declare(strict_types=1);

namespace LaminasTest\i18n\Translator\Value;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Value\TranslatorFilePattern;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class TranslatorFilePatternTest extends TestCase
{
    public function testANonExistingDirectoryIsExceptional(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The base directory must be a readable directory on the local filesystem.');

        new TranslatorFilePattern(
            'foo',
            __DIR__ . '/not-there',
            'foo-%s.txt',
            'baz',
        );
    }

    public function testTheFileNamePatternMustContainAPlaceholder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'File name patterns should contain exactly one placeholder "%s" to receive the locale',
        );

        new TranslatorFilePattern(
            'foo',
            __DIR__,
            'foo.txt',
            'baz',
        );
    }

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
            [null, __DIR__, '%s.txt', 'domain', 'type'],
            ['', __DIR__, '%s.txt', 'domain', 'type'],
            [1, __DIR__, '%s.txt', 'domain', 'type'],
            ['foo', null, '%s.txt', 'domain', 'base_dir'],
            ['foo', '', '%s.txt', 'domain', 'base_dir'],
            ['foo', 1, '%s.txt', 'domain', 'base_dir'],
            ['foo', __DIR__, null, 'domain', 'pattern'],
            ['foo', __DIR__, '', 'domain', 'pattern'],
            ['foo', __DIR__, 1, 'domain', 'pattern'],
            ['foo', __DIR__, '%s.txt', [], 'text_domain'],
            ['foo', __DIR__, '%s.txt', '', 'text_domain'],
            ['foo', __DIR__, '%s.txt', 1, 'text_domain'],
        ];
    }

    #[DataProvider('invalidParamsProvider')]
    public function testAllValuesMustBeNonEmptyString(
        mixed $type,
        mixed $directory,
        mixed $pattern,
        mixed $textDomain,
        string $invalidKey,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The key "%s" must be set and should contain a non-empty string',
            $invalidKey,
        ));

        TranslatorFilePattern::fromArray([
            'type'        => $type,
            'base_dir'    => $directory,
            'pattern'     => $pattern,
            'text_domain' => $textDomain,
        ], 'foo');
    }
}
