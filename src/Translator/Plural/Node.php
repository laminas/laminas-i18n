<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Plural;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class Node
{
    /** @param array{0?:int|self, 1?: self, 2?: self} $arguments */
    public function __construct(
        public string $id,
        public array $arguments,
    ) {
    }
}
