<?php

declare(strict_types=1);

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
     */
    protected ?int $maxDecimals = null;

    /**
     * The minimum number of decimals to use.
     */
    protected ?int $minDecimals = null;

    /**
     * NumberFormat style to use
     */
    protected ?int $formatStyle = null;

    /**
     * NumberFormat type to use
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
     * @var array<int, string>
     */
    protected array $textAttributes = [];

    /**
     * Locale to use instead of the default
     */
    protected ?string $locale = null;

    /**
     * Format a number
     *
     * @param  int|float           $number
     * @param  array<int, string>  $textAttributes
     */
    public function __invoke(
        $number,
        ?int $formatStyle = null,
        ?int $formatType = null,
        ?string $locale = null,
        ?int $maxDecimals = null,
        ?array $textAttributes = null,
        ?int $minDecimals = null
    ): string {
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
     */
    public function setFormatStyle(int $formatStyle): self
    {
        $this->formatStyle = $formatStyle;
        return $this;
    }

    /**
     * Get the format style to use
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
     */
    public function setFormatType(?int $formatType): self
    {
        $this->formatType = $formatType;
        return $this;
    }

    /**
     * Get the format type to use
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
     */
    public function setDecimals(?int $decimals): self
    {
        $this->minDecimals = $decimals;
        $this->maxDecimals = $decimals;
        return $this;
    }

    /**
     * Set the maximum number of decimals to use instead of the default.
     */
    public function setMaxDecimals(?int $maxDecimals): self
    {
        $this->maxDecimals = $maxDecimals;
        return $this;
    }

    /**
     * Set the minimum number of decimals to use instead of the default.
     */
    public function setMinDecimals(?int $minDecimals): self
    {
        $this->minDecimals = $minDecimals;
        return $this;
    }

    /**
     * Get number of decimals.
     */
    public function getDecimals(): ?int
    {
        return $this->maxDecimals;
    }

    /**
     * Get the maximum number of decimals.
     */
    public function getMaxDecimals(): ?int
    {
        return $this->maxDecimals;
    }

    /**
     * Get the minimum number of decimals.
     */
    public function getMinDecimals(): ?int
    {
        return $this->minDecimals;
    }

    /**
     * Set locale to use instead of the default.
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get the locale to use
     */
    public function getLocale(): string
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * @return array<int, string>
     */
    public function getTextAttributes(): array
    {
        return $this->textAttributes;
    }

    /**
     * @param array<int, string> $textAttributes
     */
    public function setTextAttributes(array $textAttributes): self
    {
        $this->textAttributes = $textAttributes;
        return $this;
    }
}
