<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\LoaderPluginManagerInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\I18n\TestCase;
use Psr\Container\ContainerInterface;

final class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined(): void
    {
        $pluginManager = self::createStub(LoaderPluginManagerInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap([
                ['config', []],
                [LoaderPluginManagerInterface::class, $pluginManager],
            ]);

        $factory    = new TranslatorServiceFactory();
        $translator = $factory($container, Translator::class);
        self::assertInstanceOf(Translator::class, $translator);
    }

    public function testCreateServiceWithNoTranslatorPluginManagerDefined(): void
    {
        $serviceManager = new ServiceManager([
            'services' => [
                'config' => [],
            ],
        ]);

        $factory = new TranslatorServiceFactory();
        $this->expectException(ServiceNotFoundException::class);

        $factory->__invoke($serviceManager, 'whatever', []);
    }
}
