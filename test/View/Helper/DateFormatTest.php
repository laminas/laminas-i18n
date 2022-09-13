<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use DateTime;
use DateTimeInterface;
use IntlDateFormatter;
use IntlGregorianCalendar;
use Laminas\I18n\View\Helper\DateFormat as DateFormatHelper;
use LaminasTest\I18n\TestCase;
use Locale;

use function date_default_timezone_set;
use function str_replace;

class DateFormatTest extends TestCase
{
    private DateFormatHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new DateFormatHelper();
    }

    /** @return array<array-key, array{0:string,1:string,2:int,3:int,4:DateTime}> */
    public function dateTestsDataProvider(): array
    {
        $date = new DateTime('2012-07-02T22:44:03Z');

        return [
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
        ];
    }

    /** @return array<array-key, array{0:string,1:string,2:int,3:int,4:string, 5:DateTime}> */
    public function dateTestsDataProviderWithPattern(): array
    {
        $date = new DateTime('2012-07-02T22:44:03Z');

        return [
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'dd-MM',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'MMMM',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'MMMM.Y',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'dd/Y',
                $date,
            ],
        ];
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testBasic(
        string $locale,
        string $timezone,
        int $timeType,
        int $dateType,
        DateTimeInterface $date
    ): void {
        $this->helper->setTimezone($timezone);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone)
                         ->format($date->getTimestamp());

        self::assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType,
            $locale,
            null
        ));
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testSettersProvideDefaults(
        string $locale,
        string $timezone,
        int $timeType,
        int $dateType,
        DateTimeInterface $date
    ): void {
        $this->helper
            ->setTimezone($timezone)
            ->setLocale($locale);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone)
                         ->format($date->getTimestamp());

        self::assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType
        ));
    }

    /**
     * @dataProvider dateTestsDataProviderWithPattern
     */
    public function testUseCustomPattern(
        string $locale,
        string $timezone,
        int $timeType,
        int $dateType,
        string $pattern,
        DateTimeInterface $date
    ): void {
        $this->helper
             ->setTimezone($timezone);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone, $pattern)
                         ->format($date->getTimestamp());

        self::assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType,
            $locale,
            $pattern
        ));
    }

    public function testDefaultLocale(): void
    {
        self::assertEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function testBugTwoPatternOnSameHelperInstance(): void
    {
        $date = new DateTime('2012-07-02T22:44:03Z');

        $helper = new DateFormatHelper();
        $helper->setTimezone('Europe/Berlin');
        self::assertEquals(
            '03/2012',
            $helper->__invoke($date, IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'it_IT', 'dd/Y')
        );
        self::assertEquals(
            '03-2012',
            $helper->__invoke($date, IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'it_IT', 'dd-Y')
        );
    }

    public static function assertMbStringEquals(string $expected, string $test, string $message = ''): void
    {
        $expected = str_replace(["\xC2\xA0", ' '], '', $expected);
        $test     = str_replace(["\xC2\xA0", ' '], '', $test);
        self::assertEquals($expected, $test, $message);
    }

    public function getIntlDateFormatter(
        string $locale,
        int $dateType,
        int $timeType,
        string $timezone,
        ?string $pattern = null
    ): IntlDateFormatter {
        return new IntlDateFormatter($locale, $dateType, $timeType, $timezone, null, $pattern ?? '');
    }

    public function testDifferentTimezone(): void
    {
        $helper = $this->helper;

        date_default_timezone_set('America/Chicago');
        $date = new DateTime('2018-01-01');

        self::assertSame('Jan 1, 2018', $helper($date, IntlDateFormatter::MEDIUM));

        date_default_timezone_set('America/New_York');
        $date = new DateTime('2018-01-01');

        self::assertSame('Jan 1, 2018', $helper($date, IntlDateFormatter::MEDIUM));
    }

    public function testIntlCalendarIsHandledAsWell(): void
    {
        $calendar = new IntlGregorianCalendar(2013, 6, 1);

        $helper = new DateFormatHelper();
        $helper->setTimezone('Europe/Berlin');
        self::assertEquals(
            '01-07-2013',
            $helper->__invoke($calendar, IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'it_IT', 'dd-MM-Y')
        );
    }
}
