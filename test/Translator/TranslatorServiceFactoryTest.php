<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\Placeholder\HandlebarPlaceholder;
use Laminas\I18n\Translator\PlaceholderPluginManager;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use LaminasTest\I18n\TestCase;
use Psr\Container\ContainerInterface;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined(): void
    {
        $pluginManagerMock      = $this->createMock(LoaderPluginManager::class);
        $placeholderManagerMock = $this->createMock(PlaceholderPluginManager::class);

        $serviceLocator = $this->createMock(ContainerInterface::class);
        $serviceLocator->expects(self::once())
            ->method('has')
            ->with('TranslatorPluginManager')
            ->willReturn(true);

        $placeholderManagerMock->expects(self::once())
           ->method('has')
           ->with('handlebars')
           ->willReturn(true);

        $placeholderManagerMock->expects(self::once())
           ->method('get')
           ->with('handlebars')
           ->willReturn(new HandlebarPlaceholder());

        $serviceLocator->expects(self::exactly(3))
            ->method('get')
            ->willReturnMap([
                ['TranslatorPluginManager', $pluginManagerMock],
                [PlaceholderPluginManager::class, $placeholderManagerMock],
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

        $placeholderManagerMock = $this->createMock(PlaceholderPluginManager::class);
        $serviceLocator->expects(self::exactly(2))
           ->method('get')
           ->willReturnMap([
               [PlaceholderPluginManager::class, $placeholderManagerMock],
               ['config', []],
           ]);

        $placeholderManagerMock->expects(self::once())
           ->method('has')
           ->with('handlebars')
           ->willReturn(true);

        $placeholderManagerMock->expects(self::once())
           ->method('get')
           ->with('handlebars')
           ->willReturn(new HandlebarPlaceholder());

        $factory    = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, Translator::class);
        self::assertInstanceOf(Translator::class, $translator);
    }
}
