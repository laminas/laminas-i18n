<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Translator\TextDomain;

/**
 * Remote loader interface.
 */
interface RemoteLoaderInterface
{
    /**
     * Load translations from a remote source.
     *
     * @param non-empty-string $locale
     * @param non-empty-string $textDomain
     */
    public function load(string $locale, string $textDomain): TextDomain|null;
}
