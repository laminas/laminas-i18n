<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Placeholder;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Exception\ParseException;
use Laminas\Stdlib\ArrayUtils;
use Throwable;
use Traversable;

use function iterator_to_array;
use function str_replace;
use function strlen;
use function strtoupper;
use function ucfirst;
use function uksort;

class SegmentPlaceholder implements PlaceholderInterface
{
    public function compile(string $locale, string $message, iterable $placeholders = []): string
    {
        if ($placeholders instanceof Traversable) {
            $placeholders = iterator_to_array($placeholders);
        }

        if (empty($placeholders)) {
            return $message;
        }

        if (! ArrayUtils::hasStringKeys($placeholders)) {
            throw new InvalidArgumentException(
                'SegmentPlaceholder expects an associative array of placeholder names and values'
            );
        }

        try {
            // Sorting the array by key length to replace placeholders with longer names first
            // to avoid replacing placeholders with shorter names that are part of longer names
            uksort($placeholders, static function (string|int $a, string|int $b) {
                return strlen((string) $a) <=> strlen((string) $b);
            });

            $compiled = $message;
            foreach ($placeholders as $key => $value) {
                $key      = (string) $key;
                $compiled = str_replace([':' . $key, ':' . strtoupper($key), ':' . ucfirst($key)], [
                    $value,
                    strtoupper($value),
                    ucfirst($value),
                ], $compiled);
            }
        } catch (Throwable $e) {
            throw new ParseException(
                'An error occurred while replacing placeholders in the message',
                0,
                $e
            );
        }

        return $compiled;
    }
}
