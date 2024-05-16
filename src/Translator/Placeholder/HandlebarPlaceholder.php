<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Placeholder;

use function str_replace;

class HandlebarPlaceholder implements PlaceholderInterface
{
    /**
     * @param iterable<string, string> $placeholders
     */
    public function compile(string $locale, string $message, iterable $placeholders = []): string
    {
        $compiled = $message;
        foreach ($placeholders as $key => $value) {
            $compiled = str_replace("{{{$key}}}", $value, $compiled);
        }

        return $compiled;
    }
}
