<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use ArrayObject;
use Laminas\I18n\ConfigProvider;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TranslationCollector\FileListCollector;
use Laminas\I18n\Translator\TranslationCollector\FilePatternCollector;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function is_array;
use function iterator_to_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final readonly class TestHelper
{
    public static function loaderManager(): MessageLoaderPluginManagerInterface
    {
        return new LoaderPluginManager(self::containerWithConfig());
    }

    public static function containerWithConfig(iterable $config = []): ContainerInterface
    {
        $useObject = ! is_array($config);

        $config = iterator_to_array($config);
        $config = array_replace_recursive(
            (new ConfigProvider())->__invoke(),
            $config,
        );

        $config = array_replace_recursive(
            $config,
            [
                'dependencies' => [
                    'services' => [
                        'config' => $useObject ? new ArrayObject($config) : $config,
                    ],
                ],
            ],
        );

        /** @psalm-var ServiceManagerConfiguration $dependencies */
        $dependencies = $config['dependencies'];

        return new ServiceManager($dependencies);
    }

    public static function fileListCollectorWithConfig(array $config): FileListCollector
    {
        $container = self::containerWithConfig([
            'laminas-i18n' => [
                'translator' => [
                    'translation_files' => $config,
                ],
            ],
        ]);

        return $container->get(FileListCollector::class);
    }

    public static function filePatternCollectorWithConfig(array $config): FilePatternCollector
    {
        $container = self::containerWithConfig([
            'laminas-i18n' => [
                'translator' => [
                    'translation_file_patterns' => $config,
                ],
            ],
        ]);

        return $container->get(FilePatternCollector::class);
    }
}
