<?php

namespace Laminas\I18n\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Locale;
use Traversable;

use function array_key_exists;
use function assert;
use function file_exists;
use function in_array;
use function is_scalar;
use function is_string;
use function preg_match;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;

/**
 * @deprecated This class is deprecated and will be removed in v3.0.0
 *             Use Laminas\I18n\PhoneNumber\Validator\PhoneNumber instead:
 *             https://github.com/laminas/laminas-i18n-phone-number
 */
class PhoneNumber extends AbstractValidator
{
    public const NO_MATCH    = 'phoneNumberNoMatch';
    public const UNSUPPORTED = 'phoneNumberUnsupported';
    public const INVALID     = 'phoneNumberInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var string[]
     */
    protected $messageTemplates = [
        self::NO_MATCH    => 'The input does not match a phone number format',
        self::UNSUPPORTED => 'The country provided is currently unsupported',
        self::INVALID     => 'Invalid type given. String expected',
    ];

    /**
     * Phone Number Patterns
     *
     * @link http://code.google.com/p/libphonenumber/source/browse/trunk/resources/PhoneNumberMetadata.xml
     *
     * @var array
     */
    protected static $phone = [];

    /**
     * ISO 3611 Country Code
     *
     * @var string
     */
    protected $country;

    /**
     * Allow Possible Matches
     *
     * @var bool
     */
    protected $allowPossible = false;

    /**
     * Allowed Types
     *
     * @var string[]
     */
    protected $allowedTypes = [
        'general',
        'fixed',
        'tollfree',
        'personal',
        'mobile',
        'voip',
        'uan',
    ];

    /**
     * Constructor for the PhoneNumber validator
     *
     * Options
     * - country | string | field or value
     * - allowed_types | array | array of allowed types
     * - allow_possible | boolean | allow possible matches aka non-strict
     *
     * @param iterable<string, mixed> $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (array_key_exists('country', $options)) {
            $this->setCountry($options['country']);
        } else {
            $country = Locale::getRegion(Locale::getDefault());
            $this->setCountry($country);
        }

        if (isset($options['allowed_types'])) {
            $this->allowedTypes($options['allowed_types']);
        }

        if (isset($options['allow_possible'])) {
            $this->allowPossible($options['allow_possible']);
        }

        parent::__construct($options);
    }

    /**
     * Allowed Types
     *
     * @param  string[]|null $types
     * @return $this|string[]
     */
    public function allowedTypes(?array $types = null)
    {
        if (null !== $types) {
            $this->allowedTypes = $types;

            return $this;
        }

        return $this->allowedTypes;
    }

    /**
     * Allow Possible
     *
     * @param  bool|null $possible
     * @return $this|bool
     */
    public function allowPossible($possible = null)
    {
        if (null !== $possible) {
            $this->allowPossible = (bool) $possible;

            return $this;
        }

        return $this->allowPossible;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country
     *
     * @param  string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Load Pattern
     *
     * @param  string        $code
     * @return array{code: string, patterns: array<string, array<string, string>>}|false
     */
    protected function loadPattern($code)
    {
        if (! isset(static::$phone[$code])) {
            if (! preg_match('/^[A-Z]{2}$/D', $code)) {
                return false;
            }

            $file = __DIR__ . '/PhoneNumber/' . $code . '.php';
            if (! file_exists($file)) {
                return false;
            }

            static::$phone[$code] = include $file;
        }

        return static::$phone[$code];
    }

    /**
     * Returns true if and only if $value matches phone number format
     *
     * @param mixed $value
     * @param array|null $context
     * @return bool
     */
    public function isValid($value = null, $context = null)
    {
        if (! is_scalar($value)) {
            $this->error(self::INVALID);

            return false;
        }
        $this->setValue($value);

        $country        = $this->getCountry();
        $countryPattern = $this->loadPattern(strtoupper($country));

        if (! $countryPattern && isset($context[$country]) && is_string($context[$country])) {
            $country        = $context[$country];
            $countryPattern = $this->loadPattern(strtoupper($country));
        }

        if (! $countryPattern) {
            $this->error(self::UNSUPPORTED);

            return false;
        }

        $codeLength = strlen($countryPattern['code']);

        /*
         * Check for existence of either:
         *   1) E.123/E.164 international prefix
         *   2) International double-O prefix
         *   3) Bare country prefix
         */
        $valueNoCountry = null;
        if (0 === strpos((string) $value, '+' . $countryPattern['code'])) {
            $valueNoCountry = substr((string) $value, $codeLength + 1);
        } elseif (0 === strpos((string) $value, '00' . $countryPattern['code'])) {
            $valueNoCountry = substr((string) $value, $codeLength + 2);
        } elseif (0 === strpos((string) $value, $countryPattern['code'])) {
            $valueNoCountry = substr((string) $value, $codeLength);
        }

        // check against allowed types strict match:
        foreach ($countryPattern['patterns']['national'] as $type => $pattern) {
            assert($pattern !== '');
            if (in_array($type, $this->allowedTypes, true)) {
                // check pattern:
                if (preg_match($pattern, (string) $value)) {
                    return true;
                }

                if (isset($valueNoCountry) && preg_match($pattern, $valueNoCountry)) {
                    // this handles conditions where the country code and prefix are the same
                    return true;
                }
            }
        }

        // check for possible match:
        if ($this->allowPossible()) {
            foreach ($countryPattern['patterns']['possible'] as $type => $pattern) {
                assert($pattern !== '');
                if (in_array($type, $this->allowedTypes, true)) {
                    // check pattern:
                    if (preg_match($pattern, (string) $value)) {
                        return true;
                    }

                    if (isset($valueNoCountry) && preg_match($pattern, $valueNoCountry)) {
                        // this handles conditions where the country code and prefix are the same
                        return true;
                    }
                }
            }
        }

        $this->error(self::NO_MATCH);

        return false;
    }
}
