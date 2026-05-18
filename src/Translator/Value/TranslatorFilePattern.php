<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Value;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;

use function is_dir;
use function is_readable;
use function is_string;
use function realpath;
use function rtrim;
use function sprintf;
use function substr_count;

use const DIRECTORY_SEPARATOR;

/**
 * @internal
 *
 * @psalm-internal Laminas
 * @psalm-internal LaminasTest
 * @psalm-type TranslationFilePatternSpec = array{
 *      type: non-empty-string|class-string<FileLoaderInterface>,
 *      base_dir: non-empty-string,
 *      pattern: non-empty-string,
 *      text_domain?: non-empty-string,
 *  }
 */
final readonly class TranslatorFilePattern
{
    /** @var non-empty-string */
    public string $baseDirectory;

    /**
     * @param non-empty-string|class-string<FileLoaderInterface> $type A value that can be used to retrieve the
     *                                                                 correct file loader from the plugin manager.
     * @param non-empty-string $baseDirectory                          A readable directory on-disk containing the
     *                                                                 translation files.
     * @param non-empty-string $pattern                                A sprintf pattern with one placeholder that
     *                                                                 will be replaced with the desired locale.
     * @param non-empty-string $textDomain                             The text domain the files are associated with.
     */
    public function __construct(
        public string $type,
        string $baseDirectory,
        public string $pattern,
        public string $textDomain,
    ) {
        $directory = realpath(rtrim($baseDirectory, DIRECTORY_SEPARATOR));

        if (! is_string($directory) || $directory === '' || ! is_dir($directory) || ! is_readable($directory)) {
            throw new InvalidArgumentException(sprintf(
                'The base directory must be a readable directory on the local filesystem. Received "%s"',
                $baseDirectory,
            ));
        }

        $this->baseDirectory = $directory;

        if (substr_count($this->pattern, '%s') !== 1) {
            throw new InvalidArgumentException(sprintf(
                'File name patterns should contain exactly one placeholder "%%s" to receive the locale. Received "%s"',
                $this->pattern,
            ));
        }
    }

    /**
     * @param array<array-key, mixed> $spec
     * @param non-empty-string $defaultTextDomain
     */
    public static function fromArray(array $spec, string $defaultTextDomain): self
    {
        return new self(
            self::assertKey($spec['type'] ?? null, 'type'),
            self::assertKey($spec['base_dir'] ?? null, 'base_dir'),
            self::assertKey($spec['pattern'] ?? null, 'pattern'),
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
