<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\ServiceManager\PluginManagerInterface;

/**
 * @psalm-type InstanceType = RemoteLoaderInterface|FileLoaderInterface
 * @extends PluginManagerInterface<InstanceType>
 */
interface MessageLoaderPluginManagerInterface extends PluginManagerInterface
{
}
