<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Geography;

use Laminas\I18n\ConfigProvider;
use Laminas\I18n\Geography\CountryCodeListInterface;
use Laminas\I18n\Geography\DefaultCountryCodeList;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class DefaultCountryCodeListServiceManagerIntegrationTest extends TestCase
{
    public function testAListCanBeRetrievedByFQCNWithDefaultConfiguration(): void
    {
        $services = new ServiceManager((new ConfigProvider())->getDependencyConfig());

        $list = $services->get(DefaultCountryCodeList::class);
        self::assertInstanceOf(DefaultCountryCodeList::class, $list);
    }

    public function testAListCanBeRetrievedByInterfaceWithDefaultConfiguration(): void
    {
        $services = new ServiceManager((new ConfigProvider())->getDependencyConfig());

        $list = $services->get(CountryCodeListInterface::class);
        self::assertInstanceOf(CountryCodeListInterface::class, $list);
    }
}
