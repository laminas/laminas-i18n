<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\Translator;

use ArrayObject;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;

/**
 * Text domain.
 */
class TextDomain extends ArrayObject
{
    /**
     * Plural rule.
     *
     * @var PluralRule
     */
    protected $pluralRule;

    /**
     * Set the plural rule
     *
     * @param  PluralRule $rule
     * @return TextDomain
     */
    public function setPluralRule(PluralRule $rule)
    {
        $this->pluralRule = $rule;
        return $this;
    }

    /**
     * Get the plural rule.
     *
     * Lazy loads a default rule if none already registered
     *
     * @return PluralRule
     */
    public function getPluralRule()
    {
        if ($this->pluralRule === null) {
            $this->setPluralRule(PluralRule::fromString('nplurals=2; plural=n==1'));
        }

        return $this->pluralRule;
    }
}
