<?php

namespace Laminas\I18n\Filter;

use Laminas\Filter\FilterInterface;
use Locale;

/**
 * @psalm-type Options = array{
 *     locale: string|null,
 *     ...
 * }
 * @template TOptions of Options
 * @implements AbstractFilter<TOptions>
 */
abstract class AbstractLocale implements FilterInterface
{
    public function __construct()
    {
    }

    /**
     * Sets the locale option
     *
     * @param  string|null $locale
     * @return $this
     */
    public function setLocale($locale = null)
    {
        $this->options['locale'] = $locale;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        if (! isset($this->options['locale'])) {
            $this->options['locale'] = Locale::getDefault();
        }
        return $this->options['locale'];
    }

     /**
     * Defined by Laminas\Filter\FilterInterface
     */
    abstract public function filter(mixed $value): mixed;

    /**
     * Defined by Laminas\Filter\FilterInterface
     */
    public function __invoke(mixed $value): mixed
    {
        return $this->filter($value);
    }
}
