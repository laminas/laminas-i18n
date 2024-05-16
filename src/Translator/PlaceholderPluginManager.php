<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function gettype;
use function is_object;
use function sprintf;

/**
 * Plugin manager implementation for translation placeholder compilers.
 *
 * Enforces that placeholder compilers retrieved are either instances of
 * Placeholder\PlaceholderInterface. Additionally, it registers a number
 * of default placeholder compilers.
 *
 * @template InstanceType of Placeholder\PlaceholderInterface
 * @extends AbstractPluginManager<InstanceType>
 * @method Placeholder\PlaceholderInterface get(string $name, ?array $options = null)
 */
class PlaceholderPluginManager extends AbstractPluginManager
{
    /** @inheritDoc */
    protected $aliases = [
        'segment'    => Placeholder\SegmentPlaceholder::class,
        'colon'      => Placeholder\SegmentPlaceholder::class,
        'laravel'    => Placeholder\SegmentPlaceholder::class,
        'handlebar'  => Placeholder\HandlebarPlaceholder::class,
        'handlebars' => Placeholder\HandlebarPlaceholder::class,
        'icu'        => Placeholder\IcuPlaceholder::class,
        'vsprintf'   => Placeholder\PrintfPlaceholder::class,
        'sprintf'    => Placeholder\PrintfPlaceholder::class,
        'printf'     => Placeholder\PrintfPlaceholder::class,
    ];

    /** @inheritDoc */
    protected $factories = [
        Placeholder\SegmentPlaceholder::class   => InvokableFactory::class,
        Placeholder\HandlebarPlaceholder::class => InvokableFactory::class,
        Placeholder\IcuPlaceholder::class       => InvokableFactory::class,
        Placeholder\PrintfPlaceholder::class    => InvokableFactory::class,
    ];

    /**
     * Validate the plugin.
     *
     * Checks that the filter loaded is an instance of
     * Loader\FileLoaderInterface or Loader\RemoteLoaderInterface.
     *
     * @throws Exception\RuntimeException If invalid.
     * @psalm-assert RemoteLoaderInterface $instance
     */
    public function validate(mixed $instance): void
    {
        if ($instance instanceof Placeholder\PlaceholderInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s',
            is_object($instance) ? $instance::class : gettype($instance),
            Placeholder\PlaceholderInterface::class
        ));
    }
}
