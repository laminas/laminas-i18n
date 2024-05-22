<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Formatter;

interface FormatterInterface
{
    /**
     * @param iterable<array-key, string> $placeholders
     */
    public function format(string $locale, string $message, iterable $placeholders = []): string;
}
