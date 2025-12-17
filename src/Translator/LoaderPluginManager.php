<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function get_debug_type;
use function sprintf;

/**
 * Plugin manager implementation for translation loaders.
 *
 * Enforces that loaders retrieved are either instances of
 * Loader\FileLoaderInterface or Loader\RemoteLoaderInterface. Additionally,
 * it registers a number of default loaders.
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @psalm-import-type InstanceType from LoaderPluginManagerInterface
 * @extends AbstractPluginManager<InstanceType>
 */
final class LoaderPluginManager extends AbstractPluginManager implements LoaderPluginManagerInterface
{
    private const CONFIGURATION = [
        'factories' => [
            Loader\Gettext::class  => InvokableFactory::class,
            Loader\Ini::class      => InvokableFactory::class,
            Loader\PhpArray::class => InvokableFactory::class,
        ],
        'aliases'   => [
            'gettext'  => Loader\Gettext::class,
            'getText'  => Loader\Gettext::class,
            'GetText'  => Loader\Gettext::class,
            'ini'      => Loader\Ini::class,
            'phparray' => Loader\PhpArray::class,
            'phpArray' => Loader\PhpArray::class,
            'PhpArray' => Loader\PhpArray::class,
        ],
    ];

    /** @inheritDoc */
    public function __construct(
        ContainerInterface $creationContext,
        array $config = [],
    ) {
        /** @var ServiceManagerConfiguration $config */
        $config = array_replace_recursive(self::CONFIGURATION, $config);
        parent::__construct($creationContext, $config);
    }

    /**
     * Validate the plugin
     *
     * Checks that the loader instance is one of Loader\FileLoaderInterface or Loader\RemoteLoaderInterface
     *
     * @throws InvalidServiceException
     * @psalm-assert InstanceType $plugin
     */
    public function validate(mixed $instance): void
    {
        if ($instance instanceof FileLoaderInterface || $instance instanceof RemoteLoaderInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s or %s',
            get_debug_type($instance),
            FileLoaderInterface::class,
            RemoteLoaderInterface::class,
        ));
    }
}
