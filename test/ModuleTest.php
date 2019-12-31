<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Module;
use PHPUnit_Framework_TestCase as TestCase;

class ModuleTest extends TestCase
{
    public function setUp()
    {
        $this->module = new Module();
    }

    public function testConfigReturnsExpectedKeys()
    {
        $config = $this->module->getConfig();
        $this->assertInternalType('array', $config);
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
