<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function array_shift;
use function count;
use function is_array;
use function is_file;
use function is_readable;
use function is_string;
use function sprintf;
use function stream_resolve_include_path;

/**
 * A translation file loader for files in PHP's ini format
 */
final readonly class Ini implements FileLoaderInterface
{
    public function __construct(private IniFileReader $fileReader)
    {
    }

    public function load(string $locale, string $filename): TextDomain|null
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        $fromIncludePath     = $resolvedIncludePath !== false ? $resolvedIncludePath : $filename;
        if (! $fromIncludePath || ! is_file($fromIncludePath) || ! is_readable($fromIncludePath)) {
            throw new InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename,
            ));
        }

        $messages           = [];
        $messagesNamespaced = $this->fileReader->read($fromIncludePath);

        $list = $messagesNamespaced;
        if (isset($messagesNamespaced['translation']) && is_array($messagesNamespaced['translation'])) {
            $list = $messagesNamespaced['translation'];
        }

        foreach ($list as $message) {
            if (! is_array($message) || count($message) < 2) {
                throw new InvalidArgumentException(
                    'Each INI row must be an array with message and translation',
                );
            }

            /** @psalm-var mixed $key */
            $key = $message['message'] ?? null;
            /** @psalm-var mixed $value */
            $value = $message['translation'] ?? null;

            if (is_string($key) && is_string($value)) {
                $messages[$key] = $value;
                continue;
            }

            /** @psalm-var mixed $key */
            $key = array_shift($message);
            /** @psalm-var mixed $value */
            $value = array_shift($message);

            if (is_string($key) && is_string($value)) {
                $messages[$key] = $value;
            }
        }

        $textDomain = new TextDomain($messages);

        /** @psalm-var mixed $pluralForms */
        $pluralForms = $messagesNamespaced['plural']['plural_forms'] ?? null;

        if (is_string($pluralForms)) {
            $textDomain->setPluralRule(
                PluralRule::fromString($pluralForms),
            );
        }

        return $textDomain;
    }
}
