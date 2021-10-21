<?php

namespace LaminasTest\I18n\Translator\TestAsset;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;

/**
 * Test loader.
 */
class Loader implements FileLoaderInterface
{
    public $textDomain;

    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     * @param  string $filename
     * @param  string $locale
     * @return TextDomain|null
     */
    public function load($filename, $locale)
    {
        return $this->textDomain;
    }
}
