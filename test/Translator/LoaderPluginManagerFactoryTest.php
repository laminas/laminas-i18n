<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\LoaderPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\I18n\TestCase;
use Psr\Container\ContainerInterface;

class LoaderPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsUnConfiguredPluginManagerWhenNoOptionsPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory($container, 'TranslatorPluginManager');
        self::assertInstanceOf(LoaderPluginManager::class, $loaders);
        self::assertFalse($loaders->has('test'));
    }

    public function testCreateServiceReturnsUnConfiguredPluginManagerWhenNoOptionsPresent(): void
    {
        $container = $this->createMock(ServiceLocatorInterface::class);
        $factory   = new LoaderPluginManagerFactory();
        $loaders   = $factory->createService($container);
        self::assertInstanceOf(LoaderPluginManager::class, $loaders);
        self::assertFalse($loaders->has('test'));
    }

    /** @return array<array-key, array{0: string}> */
    public function provideLoader(): array
    {
        return [
            ['gettext'],
            ['getText'],
            ['GetText'],
            ['phparray'],
            ['phpArray'],
            ['PhpArray'],
        ];
    }

    /**
     * @dataProvider provideLoader
     */
    public function testFactoryCanConfigurePluginManagerViaOptions(string $loader): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory($container, 'TranslatorPluginManager', [
            'aliases' => [
                'test' => $loader,
            ],
        ]);
        self::assertInstanceOf(LoaderPluginManager::class, $loaders);
        self::assertTrue($loaders->has('test'));
    }

    /**
     * @dataProvider provideLoader
     */
    public function testCreateServiceCanConfigurePluginManagerViaOptions(string $loader): void
    {
        $container = $this->createMock(ServiceLocatorInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $factory->setCreationOptions([
            'aliases' => [
                'test' => $loader,
            ],
        ]);
        $loaders = $factory->createService($container);
        self::assertInstanceOf(LoaderPluginManager::class, $loaders);
        self::assertTrue($loaders->has('test'));
    }

    public function testConfiguresTranslatorServicesWhenFound(): void
    {
        $translator = $this->createMock(FileLoaderInterface::class);
        $config     = [
            'translator_plugins' => [
                'aliases'   => [
                    'test' => PhpArray::class,
                ],
                'factories' => [
                    'test-too' => static fn(ContainerInterface $container): FileLoaderInterface => $translator,
                ],
            ],
        ];

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', true],
            ]);

        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container, 'TranslatorPluginManager');

        self::assertInstanceOf(LoaderPluginManager::class, $translators);
        self::assertTrue($translators->has('test'));
        self::assertInstanceOf(PhpArray::class, $translators->get('test'));
        self::assertTrue($translators->has('test-too'));
        self::assertSame($translator, $translators->get('test-too'));
    }

    public function testDoesNotConfigureTranslatorServicesWhenServiceListenerPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('ServiceListener')
            ->willReturn(true);

        $container->expects(self::never())->method('get');

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container, 'TranslatorPluginManager');

        self::assertInstanceOf(LoaderPluginManager::class, $translators);
        self::assertFalse($translators->has('test'));
        self::assertFalse($translators->has('test-too'));
    }

    public function testDoesNotConfigureTranslatorServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', false],
            ]);

        $container->expects(self::never())->method('get');

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container, 'TranslatorPluginManager');

        self::assertInstanceOf(LoaderPluginManager::class, $translators);
    }

    public function testDoesNotConfigureTranslatorServicesWhenConfigServiceDoesNotContainTranslatorsConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', true],
            ]);

        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['foo' => 'bar']);

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container, 'TranslatorPluginManager');

        self::assertInstanceOf(LoaderPluginManager::class, $translators);
        self::assertFalse($translators->has('foo'));
    }
}
