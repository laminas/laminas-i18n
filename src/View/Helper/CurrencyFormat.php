<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Locale;
use NumberFormatter;

/**
 * View helper for formatting dates.
 *
 * @category   Laminas
 * @package    Laminas_I18n
 * @subpackage View
 */
class CurrencyFormat extends AbstractHelper
{
    /**
     * Locale to use instead of the default.
     *
     * @var string
     */
    protected $locale;

    /**
     * The 3-letter ISO 4217 currency code indicating the currency to use.
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Formatter instances.
     *
     * @var array
     */
    protected $formatters = array();

    /**
     * The 3-letter ISO 4217 currency code indicating the currency to use.
     *
     * @param  string $currencyCode
     * @return CurrencyFormat
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * Get the 3-letter ISO 4217 currency code indicating the currency to use.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set locale to use instead of the default.
     *
     * @param  string $locale
     * @return CurrencyFormat
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
        return $this;
    }

    /**
     * Get the locale to use.
     *
     * @return string|null
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Format a number.
     *
     * @param  float  $number
     * @param  string $currencyCode
     * @param  string $locale
     * @return string
     */
    public function __invoke(
        $number,
        $currencyCode = null,
        $locale       = null
    ) {
        if (null === $locale) {
            $locale = $this->getLocale();
        }
        if (null === $currencyCode) {
            $currencyCode = $this->getCurrencyCode();
        }

        $formatterId = md5($locale);

        if (!isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new NumberFormatter(
                $locale,
                NumberFormatter::CURRENCY
            );
        }

        return $this->formatters[$formatterId]->formatCurrency(
            $number, $currencyCode
        );
    }
}
