<?php

namespace Laminas\I18n\Translator;

use Laminas\I18n\Translator\Formatter\FormatterInterface;
use Locale;

use function is_string;
use function method_exists;

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
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        return $this->formatMessage($this->translator->translate($message, $textDomain, $locale), $params, $locale);
    }

    /**
     * @param string                      $singular
     * @param string                      $plural
     * @param int                         $number
     * @param string|null                 $textDomain
     * @param string|null                 $locale
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
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        return $this->formatMessage(
            $this->translatePlural($singular, $plural, $number, $textDomain, $locale),
            $params,
            $locale
        );
    }

    /**
     * @param iterable<string|int, string> $params
     */
    protected function formatMessage(string $message, iterable $params, string $locale): string
    {
        return $message !== '' ? $this->formatter->format($locale, $message, $params) : $message;
    }

    protected function getLocale(): string
    {
        $locale = null;
        if (method_exists($this->translator, 'getLocale')) {
            /** @var string|null $translatorLocale */
            $translatorLocale = $this->translator->getLocale();
            if (is_string($translatorLocale)) {
                $locale = $translatorLocale;
            }
        }

        return $locale ?? Locale::getDefault();
    }
}
