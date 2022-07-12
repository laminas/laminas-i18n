<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\LoaderPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\I18n\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

class LoaderPluginManagerFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactoryReturnsUnconfiguredPluginManagerWhenNoOptionsPresent(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory($container, 'TranslatorPluginManager');
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertFalse($loaders->has('test'));
    }

    public function testCreateServiceReturnsUnconfiguredPluginManagerWhenNoOptionsPresent(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $loaders = $factory->createService($container->reveal());
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertFalse($loaders->has('test'));
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
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertTrue($loaders->has('test'));
    }

    /**
     * @dataProvider provideLoader
     */
    public function testCreateServiceCanConfigurePluginManagerViaOptions(string $loader): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $factory = new LoaderPluginManagerFactory();
        $factory->setCreationOptions([
            'aliases' => [
                'test' => $loader,
            ],
        ]);
        $loaders = $factory->createService($container->reveal());
        $this->assertInstanceOf(LoaderPluginManager::class, $loaders);
        $this->assertTrue($loaders->has('test'));
    }

    public function testConfiguresTranslatorServicesWhenFound(): void
    {
        $translator = $this->prophesize(FileLoaderInterface::class)->reveal();
        $config     = [
            'translator_plugins' => [
                'aliases'   => [
                    'test' => PhpArray::class,
                ],
                'factories' => [
                    'test-too' => static fn($container): FileLoaderInterface => $translator,
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container->reveal(), 'TranslatorPluginManager');

        $this->assertInstanceOf(LoaderPluginManager::class, $translators);
        $this->assertTrue($translators->has('test'));
        $this->assertInstanceOf(PhpArray::class, $translators->get('test'));
        $this->assertTrue($translators->has('test-too'));
        $this->assertSame($translator, $translators->get('test-too'));
    }

    public function testDoesNotConfigureTranslatorServicesWhenServiceListenerPresent(): void
    {
        $translator = $this->prophesize(FileLoaderInterface::class)->reveal();
        $config     = [
            'translator_plugins' => [
                'aliases'   => [
                    'test' => PhpArray::class,
                ],
                'factories' => [
                    'test-too' => static fn($container): FileLoaderInterface => $translator,
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(true);
        $container->has('config')->shouldNotBeCalled();
        $container->get('config')->shouldNotBeCalled();

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container->reveal(), 'TranslatorPluginManager');

        $this->assertInstanceOf(LoaderPluginManager::class, $translators);
        $this->assertFalse($translators->has('test'));
        $this->assertFalse($translators->has('test-too'));
    }

    public function testDoesNotConfigureTranslatorServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(false);
        $container->get('config')->shouldNotBeCalled();

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container->reveal(), 'TranslatorPluginManager');

        $this->assertInstanceOf(LoaderPluginManager::class, $translators);
    }

    public function testDoesNotConfigureTranslatorServicesWhenConfigServiceDoesNotContainTranslatorsConfig(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['foo' => 'bar']);

        $factory     = new LoaderPluginManagerFactory();
        $translators = $factory($container->reveal(), 'TranslatorPluginManager');

        $this->assertInstanceOf(LoaderPluginManager::class, $translators);
        $this->assertFalse($translators->has('foo'));
    }
}
