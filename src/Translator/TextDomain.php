<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator;

use ArrayObject;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;

use function array_replace;

/**
 * A collection of translated messages that behaves like an array
 *
 * @extends ArrayObject<string, string|list<string|null>|null>
 */
final class TextDomain extends ArrayObject
{
    private PluralRule|null $pluralRule               = null;
    private static PluralRule|null $defaultPluralRule = null;

    public function setPluralRule(PluralRule $rule): self
    {
        $this->pluralRule = $rule;
        return $this;
    }

    /**
     * Get the plural rule.
     *
     * @psalm-return ($fallbackToDefaultRule is true ? PluralRule : PluralRule|null)
     */
    public function getPluralRule(bool $fallbackToDefaultRule = true): PluralRule|null
    {
        if ($this->pluralRule === null && $fallbackToDefaultRule) {
            return self::getDefaultPluralRule();
        }

        return $this->pluralRule;
    }

    /**
     * Checks whether the text domain has a plural rule.
     */
    public function hasPluralRule(): bool
    {
        return $this->pluralRule !== null;
    }

    /**
     * Returns a shared default plural rule.
     */
    public static function getDefaultPluralRule(): PluralRule
    {
        if (self::$defaultPluralRule === null) {
            self::$defaultPluralRule = PluralRule::fromString('nplurals=2; plural=n != 1;');
        }

        return self::$defaultPluralRule;
    }

    /**
     * Merge another text domain with the current one.
     *
     * The plural rule of both text domains must be compatible for a successful
     * merge. We are only validating the number of plural forms though, as the
     * same rule could be made up with different expression.
     *
     * @return $this
     * @throws RuntimeException
     */
    public function merge(TextDomain $textDomain): self
    {
        if ($this->hasPluralRule() && $textDomain->hasPluralRule()) {
            if ($this->getPluralRule()->getNumPlurals() !== $textDomain->getPluralRule()->getNumPlurals()) {
                throw new RuntimeException(
                    'Plural rule of merging text domain is not compatible with the current one'
                );
            }
        } elseif ($textDomain->hasPluralRule()) {
            $this->setPluralRule($textDomain->getPluralRule());
        }

        $this->exchangeArray(
            array_replace(
                $this->getArrayCopy(),
                $textDomain->getArrayCopy()
            )
        );

        return $this;
    }

    /**
     * This method exists only to squash `Undefined array key` warnings from PHP
     *
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (! isset($this[$offset])) {
            return null;
        }

        return parent::offsetGet($offset);
    }
}
