<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector\Factory;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\TranslationCollector\AggregateCollector;
use Laminas\I18n\Translator\TranslationCollector\FileListCollector;
use Laminas\I18n\Translator\TranslationCollector\FilePatternCollector;
use Laminas\I18n\Translator\TranslationCollector\RemoteListCollector;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_iterable;
use function is_string;
use function iterator_to_array;

final readonly class AggregateCollectorFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): AggregateCollector {
        /**
         * Defines the default collectors (And the order of them) that will be instantiated
         * when no configuration is present.
         */
        $defaultCollectors = [
            RemoteListCollector::class,
            FilePatternCollector::class,
            FileListCollector::class,
        ];

        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        $i18n = $config['laminas-i18n'] ?? [];
        assert(is_array($i18n));

        $translator = $i18n['translator'] ?? [];
        assert(is_array($translator));

        $collectorList = $translator['aggregate_collector'] ?? $defaultCollectors;
        assert(is_array($collectorList));

        $collectors = [];

        foreach ($collectorList as $id) {
            if (! is_string($id)) {
                throw new InvalidArgumentException('Each item in the `aggregate_collector` list must be a string');
            }

            $collector = $container->get($id);
            assert($collector instanceof TranslationCollectorInterface);

            $collectors[] = $collector;
        }

        return new AggregateCollector($collectors);
    }
}
