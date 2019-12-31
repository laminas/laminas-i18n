<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\View\Helper\AbstractHelper;

/**
 * Helper for rendering text based on a count number (like the I18n plural translation helper, but when translation
 * is not needed).
 *
 * Please note that we did not write any hard-coded rules for languages, as languages can evolve, we prefered to
 * let the developer define the rules himself, instead of potentially break applications if we change rules in the
 * future.
 *
 * However, you can find most of the up-to-date plural rules for most languages in those links:
 *      - http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
 *      - https://developer.mozilla.org/en-US/docs/Localization_and_Plurals
 */
class Plural extends AbstractHelper
{
    /**
     * Rule to use
     *
     * @var PluralRule
     */
    protected $rule;

    /**
     * Set the plural rule to use
     *
     * @param  PluralRule|string $pluralRule
     * @return Plural
     */
    public function setPluralRule($pluralRule)
    {
        if (!$pluralRule instanceof PluralRule) {
            $pluralRule = PluralRule::fromString($pluralRule);
        }

        $this->rule = $pluralRule;

        return $this;
    }

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
        if ($this->rule === null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'No plural rule was set'
            ));
        }

        if (!is_array($strings)) {
            $strings = (array) $strings;
        }

        $pluralIndex = $this->rule->evaluate($number);

        return $strings[$pluralIndex];
    }
}
