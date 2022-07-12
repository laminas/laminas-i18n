<?php

declare(strict_types=1);

namespace LaminasTest\I18n;

use Laminas\I18n\Module;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

class ModuleTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->module = new Module();
    }

    public function testConfigReturnsExpectedKeys()
    {
        $config = $this->module->getConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('filters', $config);
        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey('validators', $config);
        $this->assertArrayHasKey('view_helpers', $config);
    }

    public function testInitRegistersPluginManagerSpecificationWithServiceListener()
    {
        $serviceListener = $this->prophesize(TestAsset\ServiceListenerInterface::class);
        $serviceListener->addServiceManager(
            'TranslatorPluginManager',
            'translator_plugins',
            'Laminas\ModuleManager\Feature\TranslatorPluginProviderInterface',
            'getTranslatorPluginConfig'
        )->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ServiceListener')->willReturn($serviceListener->reveal());

        $event = $this->prophesize(TestAsset\ModuleEventInterface::class);
        $event->getParam('ServiceManager')->willReturn($container->reveal());

        $moduleManager = $this->prophesize(TestAsset\ModuleManagerInterface::class);
        $moduleManager->getEvent()->willReturn($event->reveal());

        $this->assertNull($this->module->init($moduleManager->reveal()));
    }
}
