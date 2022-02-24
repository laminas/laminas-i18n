<?php

namespace Laminas\I18n\View\Helper;

use DateTimeInterface;
use IntlCalendar;
use IntlDateFormatter;
use Locale;

use function date_default_timezone_get;
use function md5;

/**
 * View helper for formatting dates.
 * @psalm-suppress DeprecatedClass
 */
class DateFormat extends AbstractHelper
{
    /**
     * Locale to use instead of the default
     *
     * @var string
     */
    protected $locale;

    /**
     * Timezone to use
     *
     * @var string
     */
    protected $timezone;

    /**
     * Formatter instances
     *
     * @var array<string, IntlDateFormatter>
     */
    protected $formatters = [];

    /**
     * Format a date
     *
     * @param  DateTimeInterface|IntlCalendar|int|array $date
     * @param  int                                      $dateType
     * @param  int                                      $timeType
     * @param  string|null                              $locale
     * @param  string|null                              $pattern
     * @return string
     */
    public function __invoke(
        $date,
        $dateType = IntlDateFormatter::NONE,
        $timeType = IntlDateFormatter::NONE,
        $locale = null,
        $pattern = null
    ) {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $timezone    = $this->getTimezone();
        $formatterId = md5($dateType . "\0" . $timeType . "\0" . $locale . "\0" . (string) $pattern . "\0" . $timezone);

        if (! isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new IntlDateFormatter(
                $locale,
                $dateType,
                $timeType,
                $timezone,
                IntlDateFormatter::GREGORIAN,
                $pattern ?? ''
            );
        }

        return $this->formatters[$formatterId]->format($date);
    }

    /**
     * Set locale to use instead of the default
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
     * Set timezone to use instead of the default
     *
     * @param  string $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        foreach ($this->formatters as $formatter) {
            $formatter->setTimeZone($this->timezone);
        }

        return $this;
    }

    /**
     * Get the timezone to use
     *
     * @return string
     */
    public function getTimezone()
    {
        if (! $this->timezone) {
            return date_default_timezone_get();
        }

        return $this->timezone;
    }
}
