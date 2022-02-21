<?php

namespace Laminas\I18n\Translator;

/**
 * Translator interface.
 */
interface TranslatorInterface
{
    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null);

    /**
     * Translate a plural message.
     *
     * @param  string      $singular
     * @param  string      $plural
     * @param  int         $number
     * @param  string      $textDomain
     * @param  string|null $locale
     * @return string
     */
    public function translatePlural(
        $singular,
        $plural,
        $number,
        $textDomain = 'default',
        $locale = null
    );
}
