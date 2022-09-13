<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper\Container;

use Laminas\Escaper\Escaper;
use Laminas\I18n\Geography\CountryCodeListInterface;
use Laminas\I18n\Geography\DefaultCountryCodeList;
use Laminas\I18n\View\Helper\Container\CountryCodeDataListFactory;
use Locale;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class CountryCodeDataListFactoryTest extends TestCase
{
    private string $backupLocale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupLocale = Locale::getDefault();
    }

    protected function tearDown(): void
    {
        Locale::setDefault($this->backupLocale);
        parent::tearDown();
    }

    public function testLocaleWillBeProvidedWhenConfiguredAsAString(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::atLeast(1))
            ->method('has')
            ->willReturnMap([
                [Escaper::class, false],
                ['config', true],
            ]);

        $container->expects(self::atLeast(1))
            ->method('get')
            ->willReturnMap([
                ['config', ['locale' => 'fr_FR']],
                [CountryCodeListInterface::class, DefaultCountryCodeList::create()],
            ]);

        $factory = new CountryCodeDataListFactory();
        $helper  = $factory->__invoke($container);

        self::assertStringContainsString(
            '<option value="GB" label="Royaume-Uni">',
            $helper->__invoke()
        );
    }

    public function testTheDefaultLocaleWillBeUsedWhenNoneConfigured(): void
    {
        Locale::setDefault('de_DE');

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::atLeast(1))
            ->method('has')
            ->willReturnMap([
                [Escaper::class, false],
                ['config', false],
            ]);

        $container->expects(self::atLeast(1))
            ->method('get')
            ->willReturnMap([
                [CountryCodeListInterface::class, DefaultCountryCodeList::create()],
            ]);

        $factory = new CountryCodeDataListFactory();
        $helper  = $factory->__invoke($container);

        self::assertStringContainsString(
            '<option value="GB" label="Vereinigtes&#x20;K&#xF6;nigreich">',
            $helper->__invoke()
        );
    }

    public function testTheEscaperWillBeRetrievedFromTheContainerWhenPresent(): void
    {
        $escaper = $this->createMock(Escaper::class);
        $escaper->expects(self::atLeast(1))
            ->method('escapeHtmlAttr')
            ->willReturn('ATR');

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::atLeast(1))
            ->method('has')
            ->willReturnMap([
                [Escaper::class, true],
                ['config', false],
            ]);

        $container->expects(self::atLeast(1))
            ->method('get')
            ->willReturnMap([
                [Escaper::class, $escaper],
                [CountryCodeListInterface::class, DefaultCountryCodeList::create()],
            ]);

        $factory = new CountryCodeDataListFactory();
        $helper  = $factory->__invoke($container);

        self::assertStringContainsString(
            '<option value="GB" label="ATR">',
            $helper->__invoke()
        );
    }
}
