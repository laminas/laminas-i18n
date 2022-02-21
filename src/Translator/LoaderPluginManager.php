<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Exception;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Zend\I18n\Translator\Loader\Gettext;
use Zend\I18n\Translator\Loader\Ini;
use Zend\I18n\Translator\Loader\PhpArray;

use function get_class;
use function gettype;
use function is_object;
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
 */
class LoaderPluginManager extends AbstractPluginManager
{
    /** @var array<string, class-string> */
    protected $aliases = [
        'gettext'  => Loader\Gettext::class,
        'getText'  => Loader\Gettext::class,
        'GetText'  => Loader\Gettext::class,
        'ini'      => Loader\Ini::class,
        'phparray' => Loader\PhpArray::class,
        'phpArray' => Loader\PhpArray::class,
        'PhpArray' => Loader\PhpArray::class,

        // Legacy Zend Framework aliases
        Gettext::class  => Loader\Gettext::class,
        Ini::class      => Loader\Ini::class,
        PhpArray::class => Loader\PhpArray::class,

        // v2 normalized FQCNs
        'zendi18ntranslatorloadergettext'  => Loader\Gettext::class,
        'zendi18ntranslatorloaderini'      => Loader\Ini::class,
        'zendi18ntranslatorloaderphparray' => Loader\PhpArray::class,
    ];

    /** @var array<string, class-string> */
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
     */
    public function validate($plugin)
    {
        if ($plugin instanceof Loader\FileLoaderInterface || $plugin instanceof Loader\RemoteLoaderInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Loader\FileLoaderInterface '
            . 'or %s\Loader\RemoteLoaderInterface',
            is_object($plugin) ? get_class($plugin) : gettype($plugin),
            __NAMESPACE__,
            __NAMESPACE__
        ));
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $plugin
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\RuntimeException(sprintf(
                'Plugin of type %s is invalid; must implement %s\Loader\FileLoaderInterface '
                . 'or %s\Loader\RemoteLoaderInterface',
                is_object($plugin) ? get_class($plugin) : gettype($plugin),
                __NAMESPACE__,
                __NAMESPACE__
            ));
        }
    }
}
