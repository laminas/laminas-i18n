<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\Value\TranslationFile;

use function assert;
use function sprintf;

/**
 * Defines lists of file paths per text domain and locale
 *
 * @psalm-type TextDomainKey = non-empty-string
 * @psalm-type LocaleKey = non-empty-string
 * @psalm-type FileList = array<TextDomainKey, array<LocaleKey, list<TranslationFile>>>
 */
final readonly class FileListCollector implements TranslationCollectorInterface
{
    /** @param FileList $files */
    public function __construct(
        private array $files,
        private MessageLoaderPluginManagerInterface $loader,
    ) {
    }

    /**
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws RuntimeException If a file definition references an invalid loader alias.
     */
    public function collect(string $textDomain, string $locale): TextDomain
    {
        $result = new TextDomain();
        $files  = $this->files[$textDomain] ?? [];
        foreach ([$locale, Translator::ANY_LOCALE] as $currentLocale) {
            if (! isset($files[$currentLocale])) {
                continue;
            }

            foreach ($files[$currentLocale] as $file) {
                $loader = $this->loader->has($file->type) ? $this->loader->get($file->type) : null;

                if (! $loader instanceof FileLoaderInterface) {
                    throw new RuntimeException(sprintf(
                        'The specified loader "%s" is not a known file loader or alias',
                        $file->type,
                    ));
                }

                $messages = $loader->load($locale, $file->filename);
                assert($messages !== null);

                $result->merge($messages);
            }
        }

        return $result;
    }
}
