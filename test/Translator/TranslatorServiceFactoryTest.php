<?php

namespace LaminasTest\I18n\Translator;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use LaminasTest\I18n\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TranslatorServiceFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateServiceWithNoTranslatorKeyDefined()
    {
        $pluginManagerMock = $this->prophesize(LoaderPluginManager::class)->reveal();

        $serviceLocator = $this->prophesize(ContainerInterface::class);
        $serviceLocator->has('TranslatorPluginManager')->willReturn(true)->shouldBeCalledTimes(1);
        $serviceLocator->get('TranslatorPluginManager')->willReturn($pluginManagerMock)->shouldBeCalledTimes(1);
        $serviceLocator->get('config')->willReturn([])->shouldBeCalledTimes(1);

        $factory = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator->reveal(), Translator::class);
        $this->assertInstanceOf(Translator::class, $translator);
        $this->assertSame($pluginManagerMock, $translator->getPluginManager());
    }

    public function testCreateServiceWithNoTranslatorPluginManagerDefined()
    {
        $serviceLocator = $this->prophesize(ContainerInterface::class);
        $serviceLocator->has('TranslatorPluginManager')->willReturn(false)->shouldBeCalledTimes(1);
        $serviceLocator->get('config')->willReturn([])->shouldBeCalledTimes(1);

        $factory = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator->reveal(), Translator::class);
        $this->assertInstanceOf(Translator::class, $translator);
    }
}
