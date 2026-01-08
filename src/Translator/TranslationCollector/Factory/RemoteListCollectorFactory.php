<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector\Factory;

use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\MessageLoaderPluginManagerInterface;
use Laminas\I18n\Translator\TranslationCollector\RemoteListCollector;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_iterable;
use function is_string;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class RemoteListCollectorFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): RemoteListCollector {
        $defaults = $container->get(I18nDefaults::class);

        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        $i18n = $config['laminas-i18n'] ?? [];
        assert(is_array($i18n));

        $translator = $i18n['translator'] ?? [];
        assert(is_array($translator));

        $remotes = $translator['remote_translation'] ?? [];
        assert(is_array($remotes));

        $remoteList = [];

        foreach ($remotes as $remote) {
            if (! is_array($remote)) {
                continue;
            }

            /** @psalm-var mixed $type */
            $type = $remote['type'] ?? null;
            /** @psalm-var mixed $textDomain */
            $textDomain = $remote['text_domain'] ?? $defaults->defaultTextDomain;
            if (! is_string($type) || $type === '') {
                throw new InvalidArgumentException('The `type` key for each remote loader must be a string');
            }
            if (! is_string($textDomain) || $textDomain === '') {
                throw new InvalidArgumentException('The `text_domain` key must resolve to a non-empty-string');
            }

            $remoteList[$textDomain] ??= [];
            $remoteList[$textDomain][] = $type;
        }

        return new RemoteListCollector($remoteList, $container->get(MessageLoaderPluginManagerInterface::class));
    }
}
