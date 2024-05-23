<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Formatter;

interface FormatterInterface
{
    /**
     * @param iterable<array-key, string> $params
     */
    public function format(string $locale, string $message, iterable $params = []): string;
}
