<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Placeholder;

use Laminas\I18n\Exception\ParseException;
use Traversable;

use function call_user_func_array;
use function iterator_to_array;

class PrintfPlaceholder implements PlaceholderInterface
{
    /**
     * @param iterable<int, string> $placeholders
     */
    public function compile(string $locale, string $message, iterable $placeholders = []): string
    {
        if ($placeholders instanceof Traversable) {
            $placeholders = iterator_to_array($placeholders);
        }

        /** @var string|false $compiled */
        $compiled = call_user_func_array('vsprintf', [$message, $placeholders]);
        if ($compiled === false) {
            throw new ParseException(
                'Error occurred while processing sprintf placeholders for message "' . $message . '"'
            );
        }

        return $compiled;
    }
}
