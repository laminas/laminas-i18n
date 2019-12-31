<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use PHPUnit\Framework\TestCase;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined()
    {
        $pluginManagerMock = $this->createMock(LoaderPluginManager::class);
        $slContents        = [
            ['config', []],
            ['TranslatorPluginManager', $pluginManagerMock]
        ];

        $serviceLocator = $this->createMock(ContainerInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('has')
            ->with($this->equalTo('TranslatorPluginManager'))
            ->willReturn(true);
        $serviceLocator
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap($slContents);

        $factory = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, Translator::class);
        $this->assertInstanceOf(Translator::class, $translator);
        $this->assertSame($pluginManagerMock, $translator->getPluginManager());
    }

    public function testCreateServiceWithNoTranslatorPluginManagerDefined()
    {
        $serviceLocator = $this->createMock(ContainerInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->willReturn([]);

        $factory = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, Translator::class);
        $this->assertInstanceOf(Translator::class, $translator);
    }
}
