<?php

namespace Laminas\I18n\Filter;

use Laminas\Stdlib\StringUtils;
use Locale;

use function in_array;
use function is_array;
use function is_scalar;
use function preg_replace;

/** @final */
class Alpha extends Alnum
{
    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * Returns the string $value, removing all but alphabetic characters
     *
     * @param mixed $value
     * @return ($value is scalar ? string : ($value is list<scalar> ? list<string> : mixed))
     */
    public function filter($value)
    {
        if (! is_scalar($value) && ! is_array($value)) {
            return $value;
        }

        $whiteSpace = $this->options['allow_white_space'] ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (! StringUtils::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative [a-zA-Z] match
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
        } elseif (in_array($language, ['ja', 'ko', 'zh'], true)) {
            // Use english alphabet
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', $value);
    }
}
