<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\LoaderPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit_Framework_TestCase as TestCase;

class LoaderPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsUnconfiguredPluginManagerWhenNoOptionsPresent()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory($container, 'TranslatorPluginManager');
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertFalse($loaders->has('test'));
    }

    public function testCreateServiceReturnsUnconfiguredPluginManagerWhenNoOptionsPresent()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory->createService($container->reveal());
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertFalse($loaders->has('test'));
    }

    public function testFactoryCanConfigurePluginManagerViaOptions()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory($container, 'TranslatorPluginManager', ['aliases' => [
            'test' => 'phparray',
        ]]);
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertTrue($loaders->has('test'));
    }

    public function testCreateServiceCanConfigurePluginManagerViaOptions()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $factory->setCreationOptions(['aliases' => [
            'test' => 'phparray',
        ]]);
        $loaders = $factory->createService($container->reveal());
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertTrue($loaders->has('test'));
    }
}
