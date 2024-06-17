<?php

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
 * If you are wanting to use the ability to load translation files from the
 * include_path, you will need to create a factory to override the defaults
 * defined in this class. A simple factory might look like:
 *
 * <code>
 * function ($translators) {
 *     $adapter = new Gettext();
 *     $adapter->setUseIncludePath(true);
 *     return $adapter;
 * }
 * </code>
 *
 * You may need to override the Translator service factory to make this happen
 * more easily. That can be done by extending it:
 *
 * <code>
 * use Laminas\I18n\Translator\TranslatorServiceFactory;
 * // or Laminas\Mvc\I18n\TranslatorServiceFactory
 * use Laminas\ServiceManager\ServiceLocatorInterface;
 *
 * class MyTranslatorServiceFactory extends TranslatorServiceFactory
 * {
 *     public function createService(ServiceLocatorInterface $services)
 *     {
 *         $translator = parent::createService($services);
 *         $translator->getLoaderPluginManager()->setFactory(...);
 *         return $translator;
 *     }
 * }
 * </code>
 *
 * You would then specify your custom factory in your service configuration.
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @template InstanceType of RemoteLoaderInterface|FileLoaderInterface
 * @extends AbstractPluginManager<InstanceType>
 */
class LoaderPluginManager extends AbstractPluginManager
{
    private const DEFAULT_CONFIGURATION = [
        'factories' => [
            Loader\Gettext::class  => InvokableFactory::class,
            Loader\Ini::class      => InvokableFactory::class,
            Loader\PhpArray::class => InvokableFactory::class,
        ],
        'aliases' => [
            'gettext'  => Loader\Gettext::class,
            'getText'  => Loader\Gettext::class,
            'GetText'  => Loader\Gettext::class,
            'ini'      => Loader\Ini::class,
            'phparray' => Loader\PhpArray::class,
            'phpArray' => Loader\PhpArray::class,
            'PhpArray' => Loader\PhpArray::class,
        ],
    ];

    /** @param ServiceManagerConfiguration $config */
    public function __construct(
        ContainerInterface $creationContext,
        array $config = [],
    ) {
        /** @var ServiceManagerConfiguration $config */
        $config = array_replace_recursive(self::DEFAULT_CONFIGURATION, $config);
        parent::__construct($creationContext, $config);
    }

    public function validate(mixed $instance): void
    {
        if ($instance instanceof RemoteLoaderInterface || $instance instanceof FileLoaderInterface) {
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
