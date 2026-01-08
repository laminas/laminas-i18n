<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

use Laminas\I18n\Exception\ExceptionInterface;
use Laminas\I18n\Translator\TextDomain;

/**
 * Collect translations into an object
 *
 * Defines an object that will load and collect translations for a text domain and locale pair.
 */
interface TranslationCollectorInterface
{
    /**
     * @param non-empty-string $textDomain
     * @param non-empty-string $locale
     * @throws ExceptionInterface
     */
    public function collect(string $textDomain, string $locale): TextDomain;
}
