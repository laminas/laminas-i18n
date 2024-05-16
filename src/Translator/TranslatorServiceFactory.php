<?php

namespace Laminas\I18n\Translator;

use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function gettype;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Translator.
 */
class TranslatorServiceFactory implements FactoryInterface
{
    /**
     * Create a Translator instance.
     *
     * @param string $requestedName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Translator
    {
        // Configure the translator
        /** @var array<string, array> $config */
        $config     = $container->get('config');
        $trConfig   = $config['translator'] ?? [];
        $translator = Translator::factory($trConfig);
        if ($container->has('TranslatorPluginManager')) {
            $translator->setPluginManager($container->get('TranslatorPluginManager'));
        }

        /** @var PlaceholderPluginManager $placeholderManager */
        $placeholderManager = $container->get(PlaceholderPluginManager::class);
        /** @var mixed $placeholderName */
        $placeholderName = $trConfig['placeholder_format'] ?? 'handlebars';
        if ($placeholderName instanceof Placeholder\PlaceholderInterface) {
            $placeholder = $placeholderName;
        } elseif (is_string($placeholderName)) {
            if (! $placeholderManager->has($placeholderName)) {
                throw new ServiceNotCreatedException(
                    sprintf('Could not find a placeholder format with the name "%s"', $placeholderName)
                );
            }

            $placeholder = $placeholderManager->get($placeholderName);
        } else {
            throw new InvalidServiceException(sprintf(
                '\'placeholder_format\' of type %s is invalid; must be a string or object that implements %s',
                is_object($placeholderName) ? $placeholderName::class : gettype($placeholderName),
                Placeholder\PlaceholderInterface::class
            ));
        }

        $translator->setPlaceholder($placeholder);

        return $translator;
    }
}
