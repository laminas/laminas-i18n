<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Plural;

use Closure;
use Laminas\I18n\Exception;

use function sprintf;

/**
 * Parser symbol.
 *
 * All properties in the symbol are defined as public for easier and faster
 * access from the applied closures. An exception are the closure properties
 * themselves, as they have to be accessed via the appropriate getter and
 * setter methods.
 *
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final class Symbol
{
    /** @var (Closure(self): self)|null */
    private Closure|null $nullDenotationGetter = null;
    /** @var (Closure(self, self): self)|null */
    private Closure|null $leftDenotationGetter = null;

    /**
     * Value used by literals.
     */
    public int|null $value = null;

    /**
     * First node value.
     */
    public Symbol|null $first = null;

    /**
     * Second node value.
     */
    public Symbol|null $second = null;

    /**
     * Third node value.
     */
    public Symbol|null $third = null;

    public function __construct(
        public readonly Parser $parser,
        /**
         * Node or token type name.
         */
        public string $id,
        /**
         * Left binding power (precedence).
         */
        public int $leftBindingPower
    ) {
    }

    /**
     * Set the null denotation getter.
     *
     * @param Closure(self): self $getter
     */
    public function setNullDenotationGetter(Closure $getter): void
    {
        $this->nullDenotationGetter = $getter;
    }

    /**
     * Set the left denotation getter.
     *
     * @param Closure(self, self): self $getter
     */
    public function setLeftDenotationGetter(Closure $getter): void
    {
        $this->leftDenotationGetter = $getter;
    }

    /**
     * Get null denotation.
     *
     * @throws Exception\ParseException
     */
    public function getNullDenotation(): Symbol
    {
        if ($this->nullDenotationGetter === null) {
            throw new Exception\ParseException(sprintf('Syntax error: %s', $this->id));
        }

        $function = $this->nullDenotationGetter;
        return $function($this);
    }

    /**
     * Get left denotation.
     *
     * @throws Exception\ParseException
     */
    public function getLeftDenotation(Symbol $left): Symbol
    {
        if ($this->leftDenotationGetter === null) {
            throw new Exception\ParseException(sprintf('Unknown operator: %s', $this->id));
        }

        $function = $this->leftDenotationGetter;
        return $function($this, $left);
    }
}
