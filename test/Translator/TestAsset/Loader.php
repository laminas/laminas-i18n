<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TestAsset;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\TextDomain;

class Loader implements FileLoaderInterface
{
    public ?TextDomain $textDomain = null;

    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     *
     * @param  string $filename
     * @param  string $locale
     * @return TextDomain|null
     */
    public function load($filename, $locale)
    {
        return $this->textDomain;
    }
}
