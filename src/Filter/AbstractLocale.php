<?php

namespace Laminas\I18n\Filter;

use Laminas\Filter\AbstractFilter;
use Locale;

abstract class AbstractLocale extends AbstractFilter
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
}
