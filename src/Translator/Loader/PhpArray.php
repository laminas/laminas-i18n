<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function gettype;
use function is_array;
use function is_file;
use function is_readable;
use function is_string;
use function sprintf;
use function stream_resolve_include_path;

/**
 * PHP array loader.
 *
 * @final
 */
class PhpArray extends AbstractFileLoader
{
    /**
     * @throws Exception\InvalidArgumentException
     */
    public function load(string $locale, string $filename): TextDomain|null
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        $fromIncludePath     = $resolvedIncludePath !== false ? $resolvedIncludePath : $filename;
        if (! $fromIncludePath || ! is_file($fromIncludePath) || ! is_readable($fromIncludePath)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }

        /** @psalm-suppress UnresolvableInclude */
        $messages = include $fromIncludePath;

        if (! is_array($messages)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array, but received %s',
                gettype($messages)
            ));
        }

        /** @var mixed $pluralForms */
        $pluralForms = $messages['']['plural_forms'] ?? null;
        unset($messages['']);

        $textDomain = new TextDomain($messages);

        if (is_string($pluralForms) && $pluralForms !== '') {
            $textDomain->setPluralRule(
                PluralRule::fromString($pluralForms),
            );
        }

        return $textDomain;
    }
}
