<?php

namespace Laminas\I18n\Translator;

interface TranslatorWithParamsInterface
{
    /**
     * Translate a message, replacing message placeholders with passed parameters
     *
     * @param iterable<array-key, string> $params
     */
    public function translateWithParams(
        string $message,
        iterable $params = [],
        string $textDomain = 'default',
        ?string $locale = null
    ): string;

    /**
     * Translate a plural message.
     *
     * @param iterable<array-key, string> $params
     */
    public function translatePluralWithParams(
        string $singular,
        string $plural,
        iterable $params = [],
        string $textDomain = 'default',
        ?string $locale = null
    ): string;
}
