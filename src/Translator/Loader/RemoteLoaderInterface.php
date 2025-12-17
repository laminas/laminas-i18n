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
     * @param  string $locale
     * @param  string $textDomain
     * @return TextDomain|null
     */
    public function load($locale, $textDomain);
}
