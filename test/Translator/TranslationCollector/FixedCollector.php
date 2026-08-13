<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\TranslationCollector\TranslationCollectorInterface;

final readonly class FixedCollector implements TranslationCollectorInterface
{
    public function __construct(public TextDomain $textDomain)
    {
    }

    public function collect(string $textDomain, string $locale): TextDomain
    {
        return $this->textDomain;
    }

    public static function make(): self
    {
        return new self(new TextDomain(['Message' => 'Translated']));
    }
}
