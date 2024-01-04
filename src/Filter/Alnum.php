<?php

namespace Laminas\I18n\Filter;

use Laminas\Stdlib\StringUtils;
use Locale;
use Traversable;

use function in_array;
use function is_array;
use function is_scalar;
use function preg_replace;

/**
 * @psalm-type Options = array{
 *     locale: string|null,
 *     allow_white_space: bool,
 *     ...
 * }
 * @extends AbstractLocale<Options>
 */
class Alnum extends AbstractLocale
{
    /** @var Options */
    protected $options = [
        'locale'            => null,
        'allow_white_space' => false,
    ];

    /**
     * Sets default option values for this instance
     *
     * @param array|Traversable|bool|null $allowWhiteSpaceOrOptions
     * @param string|null $locale
     */
    public function __construct($allowWhiteSpaceOrOptions = null, $locale = null)
    {
        parent::__construct();
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
     * @param  bool $flag
     * @return $this
     */
    public function setAllowWhiteSpace($flag = true)
    {
        $this->options['allow_white_space'] = (bool) $flag;
        return $this;
    }

    /**
     * Whether white space is allowed
     *
     * @return bool
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
     * @param mixed $value
     * @return ($value is scalar|list<scalar> ? string|list<scalar> : mixed)
     */
    public function filter($value)
    {
        if (! is_scalar($value) && ! is_array($value)) {
            return $value;
        }

        $whiteSpace = $this->options['allow_white_space'] ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (! StringUtils::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
        } elseif (in_array($language, ['ja', 'ko', 'zh'], true)) {
            // Use english alphabet
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
        }

        $value = is_scalar($value) ? (string) $value : $value;

        return preg_replace($pattern, '', $value);
    }
}
