<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Translator\TextDomain;

/**
 * File loader interface.
 */
interface FileLoaderInterface
{
    /**
     * Load translations from a file.
     *
     * @param non-empty-string $locale
     * @param non-empty-string $filename
     */
    public function load(string $locale, string $filename): TextDomain|null;
}
