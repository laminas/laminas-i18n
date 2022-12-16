<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\Escaper\Escaper;
use Laminas\I18n\Geography\DefaultCountryCodeList;
use Laminas\I18n\View\Helper\CountryCodeDataList;
use PHPUnit\Framework\TestCase;

class CountryCodeDataListTest extends TestCase
{
    private CountryCodeDataList $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helper = new CountryCodeDataList(
            DefaultCountryCodeList::create(),
            new Escaper(),
            'en_GB'
        );
    }

    public function testThatTheExpectedCountryIsPartOfTheList(): void
    {
        $value = $this->helper->__invoke();

        self::assertStringContainsString(
            '<option value="US" label="United&#x20;States">',
            $value
        );
    }

    public function testThatCountryNamesCanBeLocalised(): void
    {
        $value = $this->helper->__invoke('fr_FR');

        self::assertStringContainsString(
            '<option value="US" label="&#xC9;tats-Unis">',
            $value
        );
    }

    public function testThatCountryNamesArePresentedInTheDefaultLocaleWhenALocaleArgumentIsNotProvided(): void
    {
        $helper = new CountryCodeDataList(
            DefaultCountryCodeList::create(),
            new Escaper(),
            'de_DE'
        );

        $value = $helper->__invoke();

        self::assertStringContainsString(
            '<option value="DE" label="Deutschland">',
            $value
        );
    }

    public function testThatDataListAttributesArePresentInOutput(): void
    {
        $value = $this->helper->__invoke(null, [
            'id'  => 'fred',
            'foo' => 'bar',
        ]);

        self::assertStringContainsString(
            ' id="fred"',
            $value
        );

        self::assertStringContainsString(
            ' foo="bar"',
            $value
        );
    }
}
