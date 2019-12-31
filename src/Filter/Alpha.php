<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\Filter;

use Locale;

/**
 * @category   Laminas
 * @package    Laminas_I18n
 * @subpackage Filter
 */
class Alpha extends Alnum
{
    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * Returns the string $value, removing all but alphabetic characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->options['allow_white_space'] ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (!static::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative [a-zA-Z] match
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
        } elseif ($language == 'ja' || $language == 'ko' || $language == 'zh') {
            // Use english alphabet
            $pattern = '/[^a-zA-Z'  . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
