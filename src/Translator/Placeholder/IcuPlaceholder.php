<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Placeholder;

use MessageFormatter;
use Traversable;

use function iterator_to_array;

class IcuPlaceholder implements PlaceholderInterface
{
    public function compile(string $locale, string $message, iterable $placeholders = []): string
    {
        if ($placeholders instanceof Traversable) {
            $placeholders = iterator_to_array($placeholders);
        }

        return MessageFormatter::formatMessage($locale, $message, $placeholders);
    }
}
