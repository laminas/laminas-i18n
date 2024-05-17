<?php

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\TranslatorWithParamsInterface;

/**
 * View helper for translating messages with placeholders.
 */
class TranslateWithParams extends AbstractTranslatorHelper
{
    /**
     * Translate a message
     *
     * @param iterable<array-key, string> $params
     */
    public function __invoke(
        string $message,
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

        return $translator->translateWithParams($message, $params, $textDomain, $locale);
    }
}
