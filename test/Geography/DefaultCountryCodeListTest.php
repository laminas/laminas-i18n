<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Geography;

use Laminas\I18n\CountryCode;
use Laminas\I18n\Geography\DefaultCountryCodeList;
use PHPUnit\Framework\TestCase;

use function count;

class DefaultCountryCodeListTest extends TestCase
{
    public function testTheListContainsOnlyValueObjects(): void
    {
        self::assertContainsOnlyInstancesOf(CountryCode::class, DefaultCountryCodeList::create());
    }

    public function testThatTheCountExceeds240KnownCountryCodes(): void
    {
        $list = DefaultCountryCodeList::create();

        self::assertGreaterThan(240, count($list));
    }

    public function testThatAnArrayCanBeRetrievedContainingAllKnownCountryCodes(): void
    {
        $list = DefaultCountryCodeList::create();
        self::assertSameSize($list, $list->toArray());
    }

    public function testTheListIsCountable(): void
    {
        $list = DefaultCountryCodeList::create();

        self::assertSame(
            count($list),
            $list->count(),
        );
    }
}
