<?php

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\DeprecatedAbstractHelperHierarchyTrait;

use function is_array;

/**
 * Helper for rendering text based on a count number (like the I18n plural translation helper, but when translation
 * is not needed).
 *
 * Please note that we did not write any hard-coded rules for languages, as languages can evolve, we preferred to
 * let the developer define the rules himself, instead of potentially break applications if we change rules in the
 * future.
 *
 * However, you can find most of the up-to-date plural rules for most languages in those links:
 *      - http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
 *      - https://developer.mozilla.org/en-US/docs/Localization_and_Plurals
 */
class Plural extends AbstractHelper
{
    use DeprecatedAbstractHelperHierarchyTrait;

    /**
     * Plural rule to use
     *
     * @var PluralRule|null
     */
    protected $rule;

    /**
     * Given an array of strings, a number and, if wanted, an optional locale (the default one is used
     * otherwise), this picks the right string according to plural rules of the locale
     *
     * @param  array|string $strings
     * @param  int          $number
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function __invoke($strings, $number)
    {
        $rule = $this->getPluralRule();
        if ($rule === null) {
            throw new Exception\InvalidArgumentException('No plural rule was set');
        }

        if (! is_array($strings)) {
            $strings = (array) $strings;
        }

        $pluralIndex = $rule->evaluate($number);

        return $strings[$pluralIndex];
    }

    /**
     * Set the plural rule to use
     *
     * @param  PluralRule|string $pluralRule
     * @return $this
     */
    public function setPluralRule($pluralRule)
    {
        if (! $pluralRule instanceof PluralRule) {
            $pluralRule = PluralRule::fromString($pluralRule);
        }

        $this->rule = $pluralRule;

        return $this;
    }

    /**
     * Get the plural rule to use
     *
     * @return PluralRule|null
     */
    public function getPluralRule()
    {
        return $this->rule;
    }
}
