<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Value;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;

use function is_string;
use function sprintf;

/**
 * @internal
 *
 * @psalm-internal Laminas
 * @psalm-internal LaminasTest
 * @psalm-type TranslationFileSpec = array{
 *     type: non-empty-string|class-string<FileLoaderInterface>,
 *     filename: non-empty-string,
 *     locale?: non-empty-string,
 *     text_domain?: non-empty-string,
 * }
 */
final readonly class TranslationFile
{
    /**
     * @param non-empty-string|class-string<FileLoaderInterface> $type The type of file loader that should be used
     *                                                                 to load this file
     * @param non-empty-string $filename
     * @param non-empty-string $locale
     * @param non-empty-string $textDomain
     */
    public function __construct(
        public string $type,
        public string $filename,
        public string $locale,
        public string $textDomain,
    ) {
    }

    /**
     * @param array<array-key, mixed> $spec
     * @param non-empty-string $defaultLocale
     * @param non-empty-string $defaultTextDomain
     */
    public static function fromArray(array $spec, string $defaultLocale, string $defaultTextDomain): self
    {
        return new self(
            self::assertKey($spec['type'] ?? null, 'type'),
            self::assertKey($spec['filename'] ?? null, 'filename'),
            self::assertKey($spec['locale'] ?? $defaultLocale, 'locale'),
            self::assertKey($spec['text_domain'] ?? $defaultTextDomain, 'text_domain'),
        );
    }

    /** @return non-empty-string */
    private static function assertKey(mixed $value, string $key): string
    {
        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException(sprintf(
                'The key "%s" must be set and should contain a non-empty string',
                $key,
            ));
        }

        return $value;
    }
}
