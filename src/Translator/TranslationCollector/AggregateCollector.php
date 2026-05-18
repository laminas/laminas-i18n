<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\TranslationCollector;

use Laminas\I18n\Translator\TextDomain;

final readonly class AggregateCollector implements TranslationCollectorInterface
{
    /** @param list<TranslationCollectorInterface> $collectors */
    public function __construct(private array $collectors)
    {
    }

    public function collect(string $textDomain, string $locale): TextDomain
    {
        $result = new TextDomain();
        foreach ($this->collectors as $collector) {
            $result->merge($collector->collect($textDomain, $locale));
        }

        return $result;
    }
}
