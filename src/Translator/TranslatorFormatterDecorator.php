<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Translator\Formatter\FormatterInterface;

final class TranslatorFormatterDecorator implements TranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly FormatterInterface $formatter
    ) {
    }

    /**
     * @param string                      $message
     * @param string                      $textDomain
     * @param string                      $locale
     * @param iterable<array-key, string> $params
     */
    public function translate(
        $message,
        $textDomain = 'default',
        $locale = null,
        iterable $params = []
    ): string {
        $locale ??= $this->translator->getLocale();

        return $this->formatMessage($this->translator->translate($message, $textDomain, $locale), $params, $locale);
    }

    /**
     * @param string                      $singular
     * @param string                      $plural
     * @param int                         $number
     * @param string                      $textDomain
     * @param string                      $locale
     * @param iterable<array-key, string> $params
     */
    public function translatePlural(
        $singular,
        $plural,
        $number,
        $textDomain = 'default',
        $locale = null,
        iterable $params = []
    ): string {
        $locale ??= $this->translator->getLocale();

        return $this->formatMessage(
            $this->translatePlural($singular, $plural, $number, $textDomain, $locale),
            $params,
            $locale
        );
    }

    /**
     * @param iterable<string|int, string> $placeholders
     */
    protected function formatMessage(string $message, iterable $placeholders, string $locale): string
    {
        return $message !== '' ? $this->formatter->format($locale, $message, $placeholders) : $message;
    }
}
