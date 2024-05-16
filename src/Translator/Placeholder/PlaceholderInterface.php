<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Placeholder;

interface PlaceholderInterface
{
    /**
     * @param iterable<string|int, string> $placeholders
     */
    public function compile(string $locale, string $message, iterable $placeholders = []): string;
}
