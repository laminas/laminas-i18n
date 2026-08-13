<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\Value\TranslatorFilePattern;

use function assert;
use function is_file;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Loads multiple translation files based on file name patterns
 */
final readonly class FilePatternCollector implements TranslationCollectorInterface
{
    /**
     * @param array<non-empty-string, list<TranslatorFilePattern>> $patterns
     */
    public function __construct(
        private array $patterns,
        private MessageLoaderPluginManagerInterface $loader,
    ) {
    }

    /**
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws RuntimeException If a pattern references an invalid loader alias.
     */
    public function collect(string $textDomain, string $locale): TextDomain
    {
        $result   = new TextDomain();
        $patterns = $this->patterns[$textDomain] ?? [];
        foreach ($patterns as $pattern) {
            $filename = sprintf(
                '%s%s%s',
                $pattern->baseDirectory,
                DIRECTORY_SEPARATOR,
                sprintf($pattern->pattern, $locale),
            );

            if (! is_file($filename)) {
                continue;
            }

            $loader = $this->loader->has($pattern->type) ? $this->loader->get($pattern->type) : null;

            if (! $loader instanceof FileLoaderInterface) {
                throw new RuntimeException(sprintf(
                    'The specified loader "%s" is not a file loader',
                    $pattern->type,
                ));
            }

            $messages = $loader->load($locale, $filename);
            assert($messages instanceof TextDomain);

            $result->merge($messages);
        }

        return $result;
    }
}
