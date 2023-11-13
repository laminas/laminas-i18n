<?php

namespace Laminas\I18n\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\DeprecatedAbstractHelperHierarchyTrait;
use Locale;
use NumberFormatter;

use function is_array;
use function is_int;
use function md5;
use function serialize;

/**
 * View helper for formatting numbers.
 */
class NumberFormat extends AbstractHelper
{
    use DeprecatedAbstractHelperHierarchyTrait;

    /**
     * The maximum number of decimals to use.
     *
     * @var int
     */
    protected $maxDecimals;

    /**
     * The minimum number of decimals to use.
     *
     * @var int
     */
    protected $minDecimals;

    /**
     * NumberFormat style to use
     *
     * @var int
     */
    protected $formatStyle;

    /**
     * NumberFormat type to use
     *
     * @var int
     */
    protected $formatType;

    /**
     * Formatter instances
     *
     * @var array<string, NumberFormatter>
     */
    protected $formatters = [];

    /**
     * Text attributes.
     *
     * @var array<int, string>
     */
    protected $textAttributes = [];

    /**
     * Locale to use instead of the default
     *
     * @var string
     */
    protected $locale;

    /**
     * Format a number
     *
     * @param int|float          $number
     * @param int|null           $formatStyle
     * @param int|null           $formatType
     * @param string|null        $locale
     * @param int|null           $maxDecimals
     * @param array<int, string> $textAttributes
     * @param int|float          $number
     * @param int|null           $minDecimals
     * @return string
     */
    public function __invoke(
        $number,
        $formatStyle = null,
        $formatType = null,
        $locale = null,
        $maxDecimals = null,
        ?array $textAttributes = null,
        $minDecimals = null
    ) {
        if (null === $locale) {
            $locale = $this->getLocale();
        }
        if (null === $formatStyle) {
            $formatStyle = $this->getFormatStyle();
        }
        if (null === $formatType) {
            $formatType = $this->getFormatType();
        }
        if (! is_int($minDecimals) || $minDecimals < 0) {
            $minDecimals = $this->getMinDecimals();
        }
        if (! is_int($maxDecimals) || $maxDecimals < 0) {
            $maxDecimals = $this->getMaxDecimals();
        }
        if (($maxDecimals !== null) && ($minDecimals === null)) {
            // Fallback to old behavior
            $minDecimals = $maxDecimals;
        }
        if (! is_array($textAttributes)) {
            $textAttributes = $this->getTextAttributes();
        }

        $formatterId = md5(
            $formatStyle . "\0" . $locale . "\0" . ($minDecimals ?? '') . "\0" . ($maxDecimals ?? '') . "\0"
            . md5(serialize($textAttributes))
        );

        if (isset($this->formatters[$formatterId])) {
            $formatter = $this->formatters[$formatterId];
        } else {
            $formatter = new NumberFormatter($locale, $formatStyle);

            if ($minDecimals !== null) {
                $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $minDecimals);
            }

            if ($maxDecimals !== null) {
                $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxDecimals);
            }

            foreach ($textAttributes as $textAttribute => $value) {
                $formatter->setTextAttribute($textAttribute, $value);
            }

            $this->formatters[$formatterId] = $formatter;
        }

        return $formatter->format($number, $formatType);
    }

    /**
     * Set format style to use instead of the default
     *
     * @param  int $formatStyle
     * @return $this
     */
    public function setFormatStyle($formatStyle)
    {
        $this->formatStyle = (int) $formatStyle;
        return $this;
    }

    /**
     * Get the format style to use
     *
     * @return int
     */
    public function getFormatStyle()
    {
        if (null === $this->formatStyle) {
            $this->formatStyle = NumberFormatter::DECIMAL;
        }

        return $this->formatStyle;
    }

    /**
     * Set format type to use instead of the default
     *
     * @param  int $formatType
     * @return $this
     */
    public function setFormatType($formatType)
    {
        $this->formatType = (int) $formatType;
        return $this;
    }

    /**
     * Get the format type to use
     *
     * @return int
     */
    public function getFormatType()
    {
        if (null === $this->formatType) {
            $this->formatType = NumberFormatter::TYPE_DEFAULT;
        }
        return $this->formatType;
    }

    /**
     * Set number of decimals (both min & max) to use instead of the default.
     *
     * @param  int $decimals
     * @return $this
     */
    public function setDecimals($decimals)
    {
        $this->minDecimals = $decimals;
        $this->maxDecimals = $decimals;
        return $this;
    }

    /**
     * Set the maximum number of decimals to use instead of the default.
     *
     * @param int $maxDecimals
     * @return $this
     */
    public function setMaxDecimals($maxDecimals)
    {
        $this->maxDecimals = $maxDecimals;
        return $this;
    }

    /**
     * Set the minimum number of decimals to use instead of the default.
     *
     * @param int $minDecimals
     * @return $this
     */
    public function setMinDecimals($minDecimals)
    {
        $this->minDecimals = $minDecimals;
        return $this;
    }

    /**
     * Get number of decimals.
     *
     * @return int
     */
    public function getDecimals()
    {
        return $this->maxDecimals;
    }

    /**
     * Get the maximum number of decimals.
     *
     * @return int
     */
    public function getMaxDecimals()
    {
        return $this->maxDecimals;
    }

    /**
     * Get the minimum number of decimals.
     *
     * @return int
     */
    public function getMinDecimals()
    {
        return $this->minDecimals;
    }

    /**
     * Set locale to use instead of the default.
     *
     * @param  string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
        return $this;
    }

    /**
     * Get the locale to use
     *
     * @return string
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * @return array<int, string>
     */
    public function getTextAttributes()
    {
        return $this->textAttributes;
    }

    /**
     * @param array<int, string> $textAttributes
     * @return $this
     */
    public function setTextAttributes(array $textAttributes)
    {
        $this->textAttributes = $textAttributes;
        return $this;
    }
}
