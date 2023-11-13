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
 * View helper for formatting dates.
 */
class NumberFormat extends AbstractHelper
{
    use DeprecatedAbstractHelperHierarchyTrait;

    /**
     * The maximum number of decimals to use.
     *
     * @var int
     */
    protected ?int $maxDecimals = null;

    /**
     * The minimum number of decimals to use.
     *
     * @var int
     */
    protected ?int $minDecimals = null;

    /**
     * NumberFormat style to use
     *
     * @var int
     */
    protected ?int $formatStyle = null;

    /**
     * NumberFormat type to use
     *
     * @var int
     */
    protected ?int $formatType = null;

    /**
     * Formatter instances
     *
     * @var array<string, NumberFormatter>
     */
    protected array $formatters = [];

    /**
     * Text attributes.
     *
     * @var array
     */
    protected array $textAttributes = [];

    /**
     * Locale to use instead of the default
     *
     * @var string
     */
    protected ?string $locale = null;

    /**
     * Format a number
     *
     * @param  int|float   $number
     * @param  int|null    $formatStyle
     * @param  int|null    $formatType
     * @param  string|null $locale
     * @param  int|null    $maxDecimals
     * @param  array|null  $textAttributes
     * @param  int|null     $minDecimals
     *
     * @return string
     */
    public function __invoke(
        $number,
        ?int $formatStyle = null,
        ?int $formatType = null,
        ?string $locale = null,
        ?int $maxDecimals = null,
        ?array $textAttributes = null,
        ?int $minDecimals = null
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
            $formatStyle . "\0" . $locale . "\0" . $minDecimals . "\0" . $maxDecimals . "\0"
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
    public function setFormatStyle(int $formatStyle): self
    {
        $this->formatStyle = $formatStyle;
        return $this;
    }

    /**
     * Get the format style to use
     *
     * @return int
     */
    public function getFormatStyle(): int
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
    public function setFormatType($formatType): self
    {
        $this->formatType = (int) $formatType;
        return $this;
    }

    /**
     * Get the format type to use
     *
     * @return int
     */
    public function getFormatType(): int
    {
        if (null === $this->formatType) {
            $this->formatType = NumberFormatter::TYPE_DEFAULT;
        }
        return $this->formatType;
    }

    /**
     * Set number (both min & max) of decimals to use instead of the default.
     *
     * @param  int $decimals
     * @return $this
     */
    public function setDecimals($decimals): self
    {
        $this->minDecimals = (int) $decimals;
        $this->maxDecimals = (int) $decimals;
        return $this;
    }

    /**
     * Set the maximum number of decimals to use instead of the default.
     *
     * @param  int|null $maxDecimals
     * @return $this
     */
    public function setMaxDecimals(?int $maxDecimals): self
    {
        $this->maxDecimals = $maxDecimals;
        return $this;
    }

    /**
     * Set the minimum number of decimals to use instead of the default.
     *
     * @param int|null $minDecimals
     * @return $this
     */
    public function setMinDecimals(?int $minDecimals): self
    {
        $this->minDecimals = $minDecimals;
        return $this;
    }

    /**
     * Get number of decimals.
     *
     * @return int|null
     */
    public function getDecimals(): ?int
    {
        return $this->maxDecimals;
    }

    /**
     * Get the maximum number of decimals.
     *
     * @return int|null
     */
    public function getMaxDecimals(): ?int
    {
        return $this->maxDecimals;
    }

    /**
     * Get the minimum number of decimals.
     *
     * @return int|null
     */
    public function getMinDecimals(): ?int
    {
        return $this->minDecimals;
    }

    /**
     * Set locale to use instead of the default.
     *
     * @param  string $locale
     * @return $this
     */
    public function setLocale($locale): self
    {
        $this->locale = (string) $locale;
        return $this;
    }

    /**
     * Get the locale to use
     *
     * @return string
     */
    public function getLocale(): string
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * @return array
     */
    public function getTextAttributes(): array
    {
        return $this->textAttributes;
    }

    /**
     * @param array $textAttributes
     * @return $this
     */
    public function setTextAttributes(array $textAttributes): self
    {
        $this->textAttributes = $textAttributes;
        return $this;
    }
}
