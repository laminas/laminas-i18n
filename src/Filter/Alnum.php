<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\Filter;

use Locale;
use Traversable;

/**
 * @category   Laminas
 * @package    Laminas_I18n
 * @subpackage Filter
 */
class Alnum extends AbstractLocale
{
    /**
     * @var array
     */
    protected $options = array(
        'locale'            => null,
        'allow_white_space' => false,
    );

    /**
     * Sets default option values for this instance
     *
     * @param array|Traversable|boolean|null $allowWhiteSpaceOrOptions
     * @param string|null $locale
     */
    public function __construct($allowWhiteSpaceOrOptions = null, $locale = null)
    {
        if ($allowWhiteSpaceOrOptions !== null) {
            if (static::isOptions($allowWhiteSpaceOrOptions)) {
                $this->setOptions($allowWhiteSpaceOrOptions);
            } else {
                $this->setAllowWhiteSpace($allowWhiteSpaceOrOptions);
                $this->setLocale($locale);
            }
        }
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param  boolean $flag
     * @return Alnum Provides a fluent interface
     */
    public function setAllowWhiteSpace($flag = true)
    {
        $this->options['allow_white_space'] = (boolean) $flag;
        return $this;
    }

    /**
     * Whether white space is allowed
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->options['allow_white_space'];
    }

    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * Returns $value as string with all non-alphanumeric characters removed
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->options['allow_white_space'] ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (!static::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
        } elseif ($language == 'ja'|| $language == 'ko' || $language == 'zh') {
            // Use english alphabet
            $pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
