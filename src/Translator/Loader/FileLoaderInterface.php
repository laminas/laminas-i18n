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
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain|null
     */
    public function load($locale, $filename);
}
