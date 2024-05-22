<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use LaminasTest\I18n\TestCase;
use Psr\Container\ContainerInterface;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined(): void
    {
        $pluginManagerMock      = $this->createMock(LoaderPluginManager::class);

        $serviceLocator = $this->createMock(ContainerInterface::class);
        $serviceLocator->expects(self::once())
            ->method('has')
            ->with('TranslatorPluginManager')
            ->willReturn(true);

        $serviceLocator->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap([
                ['TranslatorPluginManager', $pluginManagerMock],
                ['config', []],
            ]);

        $factory    = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, Translator::class);
        self::assertInstanceOf(Translator::class, $translator);
        self::assertSame($pluginManagerMock, $translator->getPluginManager());
    }

    public function testCreateServiceWithNoTranslatorPluginManagerDefined(): void
    {
        $serviceLocator = $this->createMock(ContainerInterface::class);
        $serviceLocator->expects(self::once())
            ->method('has')
            ->with('TranslatorPluginManager')
            ->willReturn(false);

        $serviceLocator->expects(self::once())
           ->method('get')
            ->with('config')
            ->willReturn([]);

        $factory    = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, Translator::class);
        self::assertInstanceOf(Translator::class, $translator);
    }
}
