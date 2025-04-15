<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

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
 * @template InstanceType of RemoteLoaderInterface|FileLoaderInterface
 * @extends AbstractPluginManager<InstanceType>
 * @final
 */
class LoaderPluginManager extends AbstractPluginManager
{
    /** @inheritDoc */
    protected $aliases = [
        'gettext'  => Loader\Gettext::class,
        'getText'  => Loader\Gettext::class,
        'GetText'  => Loader\Gettext::class,
        'ini'      => Loader\Ini::class,
        'phparray' => Loader\PhpArray::class,
        'phpArray' => Loader\PhpArray::class,
        'PhpArray' => Loader\PhpArray::class,

        // Legacy Zend Framework aliases
        'Zend\I18n\Translator\Loader\Gettext'  => Loader\Gettext::class,
        'Zend\I18n\Translator\Loader\Ini'      => Loader\Ini::class,
        'Zend\I18n\Translator\Loader\PhpArray' => Loader\PhpArray::class,

        // v2 normalized FQCNs
        'zendi18ntranslatorloadergettext'  => Loader\Gettext::class,
        'zendi18ntranslatorloaderini'      => Loader\Ini::class,
        'zendi18ntranslatorloaderphparray' => Loader\PhpArray::class,
    ];

    /** @inheritDoc */
    protected $factories = [
        Loader\Gettext::class  => InvokableFactory::class,
        Loader\Ini::class      => InvokableFactory::class,
        Loader\PhpArray::class => InvokableFactory::class,
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'laminasi18ntranslatorloadergettext'  => InvokableFactory::class,
        'laminasi18ntranslatorloaderini'      => InvokableFactory::class,
        'laminasi18ntranslatorloaderphparray' => InvokableFactory::class,
    ];

    /**
     * Validate the plugin.
     *
     * Checks that the filter loaded is an instance of
     * Loader\FileLoaderInterface or Loader\RemoteLoaderInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException If invalid.
     * @psalm-assert InstanceType $plugin
     */
    public function validate($plugin)
    {
        if ($plugin instanceof FileLoaderInterface || $plugin instanceof RemoteLoaderInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s or %s',
            get_debug_type($plugin),
            FileLoaderInterface::class,
            RemoteLoaderInterface::class
        ));
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @deprecated Since 2.16.0 - This component is no longer compatible with Service Manager v2.
     *             This method will be removed in version 3.0
     *
     * @param mixed $plugin
     * @throws Exception\RuntimeException
     * @psalm-assert InstanceType $plugin
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException) {
            throw new Exception\RuntimeException(sprintf(
                'Plugin of type %s is invalid; must implement %s or %s',
                get_debug_type($plugin),
                FileLoaderInterface::class,
                RemoteLoaderInterface::class
            ));
        }
    }
}
