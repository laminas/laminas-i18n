<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Formatter;

use MessageFormatter;
use Traversable;

use function iterator_to_array;

class IcuFormatter implements FormatterInterface
{
    public function format(string $locale, string $message, iterable $params = []): string
    {
        if ($params instanceof Traversable) {
            $params = iterator_to_array($params);
        }

        return MessageFormatter::formatMessage($locale, $message, $params);
    }
}
