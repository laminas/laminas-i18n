<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Formatter;

use Laminas\I18n\Exception\ParseException;
use Traversable;

use function call_user_func_array;
use function iterator_to_array;

class PrintfFormatter implements FormatterInterface
{
    public function format(string $locale, string $message, iterable $params = []): string
    {
        if ($params instanceof Traversable) {
            $params = iterator_to_array($params);
        }

        /** @var string|false $compiled */
        $compiled = call_user_func_array('vsprintf', [$message, $params]);
        if ($compiled === false) {
            throw new ParseException(
                'Error occurred while processing sprintf placeholders for message "' . $message . '"'
            );
        }

        return $compiled;
    }
}
