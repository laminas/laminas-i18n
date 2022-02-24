<?php

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;

use function is_array;
use function sprintf;

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
 * @psalm-suppress DeprecatedClass
 */
class Plural extends AbstractHelper
{
    /**
     * Plural rule to use
     *
     * @var PluralRule
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
        if (null === $this->getPluralRule()) {
            throw new Exception\InvalidArgumentException(sprintf(
                'No plural rule was set'
            ));
        }

        if (! is_array($strings)) {
            $strings = (array) $strings;
        }

        $pluralIndex = $this->getPluralRule()->evaluate($number);

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
     * @return PluralRule
     */
    public function getPluralRule()
    {
        return $this->rule;
    }
}
