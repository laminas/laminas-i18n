<?php

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\TranslatorFormatterDecorator;

/**
 * View helper for translating messages with placeholders.
 */
class TranslatePluralWithParams extends AbstractTranslatorHelper
{
    /**
     * Translate a message
     *
     * @param iterable<array-key, string> $params
     * @noinspection PhpTooManyParametersInspection
     */
    public function __invoke(
        string $singular,
        string $plural,
        int $number,
        ?string $textDomain = null,
        ?string $locale = null,
        iterable $params = [],
    ): string {
        $translator = $this->getTranslator();
        if (null === $translator) {
            throw new Exception\RuntimeException('Translator has not been set');
        }
        if (! $translator instanceof TranslatorFormatterDecorator) {
            throw new Exception\RuntimeException(
                'No param support, the translator bust be decorated with TranslatorFormatterDecorator'
            );
        }
        if (null === $textDomain) {
            $textDomain = $this->getTranslatorTextDomain();
        }

        return $translator->translatePlural($singular, $plural, $number, $textDomain, $locale, $params);
    }
}
