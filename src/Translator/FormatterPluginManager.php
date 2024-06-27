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
 * Plugin manager implementation for translation message formatters.
 *
 * Enforces that placeholder compilers retrieved are either instances of
 * Formatter\FormatterInterface. Additionally, it registers a number
 * of default message formatters.
 *
 * @template InstanceType of Formatter\FormatterInterface
 * @extends AbstractPluginManager<InstanceType>
 * @method Formatter\FormatterInterface get(string $name, ?array $options = null)
 */
class FormatterPluginManager extends AbstractPluginManager
{
    /** @inheritDoc */
    protected $aliases = [
        'segment'    => Formatter\SegmentFormatter::class,
        'colon'      => Formatter\SegmentFormatter::class,
        'laravel'    => Formatter\SegmentFormatter::class,
        'handlebar'  => Formatter\HandlebarFormatter::class,
        'handlebars' => Formatter\HandlebarFormatter::class,
        'icu'        => Formatter\IcuFormatter::class,
        'vsprintf'   => Formatter\PrintfFormatter::class,
        'sprintf'    => Formatter\PrintfFormatter::class,
        'printf'     => Formatter\PrintfFormatter::class,
    ];

    /** @inheritDoc */
    protected $factories = [
        Formatter\SegmentFormatter::class   => InvokableFactory::class,
        Formatter\HandlebarFormatter::class => InvokableFactory::class,
        Formatter\IcuFormatter::class       => InvokableFactory::class,
        Formatter\PrintfFormatter::class    => InvokableFactory::class,
    ];

    /**
     * Validate the plugin.
     *
     * Checks that the filter loaded is an instance of Formatter\FormatterInterface
     *
     * @throws Exception\RuntimeException If invalid.
     * @psalm-assert RemoteLoaderInterface $instance
     */
    public function validate(mixed $instance): void
    {
        if ($instance instanceof Formatter\FormatterInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s',
            is_object($instance) ? $instance::class : gettype($instance),
            Formatter\FormatterInterface::class
        ));
    }
}
