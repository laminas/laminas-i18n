<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector\Factory;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TranslationCollector\FileListCollector;
use Laminas\I18n\Translator\Value\TranslationFile;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_iterable;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class FileListCollectorFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): FileListCollector {
        $defaults = $container->get(I18nDefaults::class);
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        $i18n = $config['laminas-i18n'] ?? [];
        assert(is_array($i18n));

        $translator = $i18n['translator'] ?? [];
        assert(is_array($translator));

        $files = $translator['translation_files'] ?? [];
        assert(is_array($files));

        $fileList = [];

        /** @psalm-var mixed $spec */
        foreach ($files as $spec) {
            if (! is_array($spec)) {
                continue;
            }

            $file = TranslationFile::fromArray(
                $spec,
                $defaults->defaultLocale,
                $defaults->defaultTextDomain,
            );

            $fileList[$file->textDomain]                ??= [];
            $fileList[$file->textDomain][$file->locale] ??= [];
            $fileList[$file->textDomain][$file->locale][] = $file;
        }

        return new FileListCollector($fileList, $container->get(MessageLoaderPluginManagerInterface::class));
    }
}
