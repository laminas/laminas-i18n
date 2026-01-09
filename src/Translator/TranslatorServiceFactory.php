<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_bool;
use function is_iterable;
use function is_string;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class TranslatorServiceFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): Translator {
        $defaults = $container->get(I18nDefaults::class);

        /**
         * Determine the default locale, allowing build-time locale to override configuration
         *
         * @psalm-var mixed $locale
         */
        $locale = $options['locale'] ?? null;
        $locale = is_string($locale) && $locale !== '' ? $locale : $defaults->defaultLocale;

        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        $i18n = $config['laminas-i18n'] ?? [];
        assert(is_array($i18n));

        $translator = $i18n['translator'] ?? [];
        assert(is_array($translator));

        /**
         * The fallback locale is optionally used to provide translations when none are available in the default, or
         * runtime locale.
         *
         * @psalm-var mixed $fallbackLocale
         */
        $fallbackLocale = $translator['fallback_locale'] ?? null;
        $fallbackLocale = is_string($fallbackLocale) && $fallbackLocale !== '' ? $fallbackLocale : null;

        /** @psalm-var mixed $enableEvents */
        $enableEvents = $translator['event_manager_enabled'] ?? false;
        $enableEvents = is_bool($enableEvents) && $enableEvents;

        $instance = new Translator(
            $container->get(TranslationCollectorInterface::class),
            $locale,
            $fallbackLocale,
            $defaults->defaultTextDomain,
        );

        if ($enableEvents) {
            $instance->enableEventManager();
        }

        return $instance;
    }
}
