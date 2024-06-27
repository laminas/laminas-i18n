<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Translator\Formatter\FormatterInterface;
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
class TranslatorFormatterDecoratorFactory implements FactoryInterface
{
    /**
     * Create a Translator instance.
     *
     * @param string $requestedName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): TranslatorFormatterDecorator {
        // Configure the translator
        /** @var array<string, array> $config */
        $config     = $container->get('config');
        $trConfig   = $config['translator'] ?? [];
        $translator = $container->get(TranslatorInterface::class);

        /** @var FormatterPluginManager $formatterPluginManager */
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        /** @var string|FormatterInterface|mixed $formatterName */
        $formatterName = $trConfig['message_format'] ?? 'handlebars';
        if ($formatterName instanceof FormatterInterface) {
            $formatter = $formatterName;
        } elseif (is_string($formatterName)) {
            if (! $formatterPluginManager->has($formatterName)) {
                throw new ServiceNotCreatedException(
                    sprintf('Could not find a placeholder format with the name "%s"', $formatterName)
                );
            }

            $formatter = $formatterPluginManager->get($formatterName);
        } else {
            throw new InvalidServiceException(sprintf(
                '\'message_format\' of type %s is invalid; must be a string or object that implements %s',
                is_object($formatterName) ? $formatterName::class : gettype($formatterName),
                FormatterInterface::class
            ));
        }

        return new TranslatorFormatterDecorator($translator, $formatter);
    }
}
