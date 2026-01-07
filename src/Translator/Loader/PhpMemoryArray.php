<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function is_array;
use function is_string;
use function sprintf;

/**
 * PHP Memory array loader.
 *
 * @psalm-type TextDomainKey = string
 * @psalm-type LocaleKey = string
 * @psalm-type MessagesShape = array<string, string|list<string|null>|array{plural_forms: string}>
 * @psalm-type ArrayShape = array<TextDomainKey, array<LocaleKey, MessagesShape>>
 */
final readonly class PhpMemoryArray implements RemoteLoaderInterface
{
    /** @param ArrayShape $messages */
    public function __construct(private array $messages)
    {
    }

    /**
     * Load translations from a remote source.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function load(string $locale, string $textDomain): TextDomain|null
    {
        if (! isset($this->messages[$textDomain])) {
            throw new Exception\InvalidArgumentException(
                sprintf('Expected textdomain "%s" to be an array, but it is not set', $textDomain)
            );
        }

        $messages = $this->messages[$textDomain][$locale] ?? null;

        if (! is_array($messages)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Expected locale "%s" to be an array, but it is not set', $locale)
            );
        }

        $pluralRule = $messages['']['plural_forms'] ?? null;
        unset($messages['']);

        $textDomain = new TextDomain($messages);

        if (is_string($pluralRule) && $pluralRule !== '') {
            $textDomain->setPluralRule(
                PluralRule::fromString($pluralRule)
            );
        }

        return $textDomain;
    }
}
