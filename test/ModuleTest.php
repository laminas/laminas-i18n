<?php

declare(strict_types=1);

namespace LaminasTest\I18n;

use Laminas\I18n\Module;
use Psr\Container\ContainerInterface;

class ModuleTest extends TestCase
{
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();
        $this->module = new Module();
    }

    public function testConfigReturnsExpectedKeys(): void
    {
        $config = $this->module->getConfig();
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        self::assertIsArray($config);
        self::assertArrayHasKey('filters', $config);
        self::assertArrayHasKey('service_manager', $config);
        self::assertArrayHasKey('validators', $config);
        self::assertArrayHasKey('view_helpers', $config);
    }

    public function testInitRegistersPluginManagerSpecificationWithServiceListener(): void
    {
        $serviceListener = $this->createMock(TestAsset\ServiceListenerInterface::class);
        $serviceListener->expects(self::once())
            ->method('addServiceManager')
            ->with(
                'TranslatorPluginManager',
                'translator_plugins',
                'Laminas\ModuleManager\Feature\TranslatorPluginProviderInterface',
                'getTranslatorPluginConfig'
            );

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('ServiceListener')
            ->willReturn($serviceListener);

        $event = $this->createMock(TestAsset\ModuleEventInterface::class);
        $event->expects(self::once())
            ->method('getParam')
            ->with('ServiceManager')
            ->willReturn($container);

        $moduleManager = $this->createMock(TestAsset\ModuleManagerInterface::class);
        $moduleManager->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        /** @psalm-suppress InvalidArgument */
        $this->module->init($moduleManager);
    }
}
