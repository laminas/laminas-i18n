<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Formatter;

use function str_replace;

class HandlebarFormatter implements FormatterInterface
{
    public function format(string $locale, string $message, iterable $params = []): string
    {
        $compiled = $message;
        foreach ($params as $key => $value) {
            $compiled = str_replace("{{{$key}}}", $value, $compiled);
        }

        return $compiled;
    }
}
