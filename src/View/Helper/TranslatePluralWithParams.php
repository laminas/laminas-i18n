<?php

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\TranslatorWithParamsInterface;

/**
 * View helper for translating messages with placeholders.
 */
class TranslatePluralWithParams extends AbstractTranslatorHelper
{
    /**
     * Translate a message
     *
     * @param iterable<array-key, string> $params
     */
    public function __invoke(
        string $singular,
        string $plural,
        iterable $params = [],
        ?string $textDomain = null,
        ?string $locale = null
    ): string {
        $translator = $this->getTranslator();
        if (null === $translator) {
            throw new Exception\RuntimeException('Translator has not been set');
        }
        if (! $translator instanceof TranslatorWithParamsInterface) {
            throw new Exception\RuntimeException(
                'No param support, the translator does not implement TranslatorWithParamsInterface'
            );
        }
        if (null === $textDomain) {
            $textDomain = $this->getTranslatorTextDomain();
        }

        return $translator->translatePluralWithParams($singular, $plural, $params, $textDomain, $locale);
    }
}
