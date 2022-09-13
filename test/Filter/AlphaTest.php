<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Filter;

use Laminas\I18n\Filter\Alpha as AlphaFilter;
use LaminasTest\I18n\TestCase;
use Locale;
use stdClass;

use function array_keys;
use function array_values;
use function preg_match;

class AlphaTest extends TestCase
{
    private AlphaFilter $filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     **/
    protected static bool $unicodeEnabled;

    /**
     * The Alphabet means english alphabet.
     */
    protected static bool $meansEnglishAlphabet;

    /**
     * Creates a new AlnumFilter object for each test method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->filter               = new AlphaFilter();
        $language                   = Locale::getPrimaryLanguage(Locale::getDefault());
        self::$meansEnglishAlphabet = $language === 'ja';
        self::$unicodeEnabled       = (bool) @preg_match('/\pL/u', 'a');
    }

    /**
     * Ensures that the filter follows expected behavior
     */
    public function testBasic(): void
    {
        if (! self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = [
                'abc123'  => 'abc',
                'abc 123' => 'abc',
                'abcxyz'  => 'abcxyz',
                ''        => '',
            ];
        } elseif (self::$meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            /**
             * The first element contains multibyte alphabets.
             *  But , AlphaFilter is expected to return only singlebyte alphabets.
             * The second contains multibyte or singlebyte space.
             * The third  contains multibyte or singlebyte digits.
             * The forth  contains various multibyte or singlebyte characters.
             * The last contains only singlebyte alphabets.
             */
            $valuesExpected = [
                'aＡBｂc'       => 'aBc',
                'z Ｙ　x'       => 'zx',
                'Ｗ1v３Ｕ4t'     => 'vt',
                '，sй.rλ:qν＿p' => 'srqp',
                'onml'        => 'onml',
            ];
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = [
                'abc123'     => 'abc',
                'abc 123'    => 'abc',
                'abcxyz'     => 'abcxyz',
                'četně'      => 'četně',
                'لعربية'     => 'لعربية',
                'grzegżółka' => 'grzegżółka',
                'België'     => 'België',
                ''           => '',
            ];
        }

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            self::assertEquals($expected, $actual);
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     */
    public function testAllowWhiteSpace(): void
    {
        $this->filter->setAllowWhiteSpace(true);

        if (! self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = [
                'abc123'  => 'abc',
                'abc 123' => 'abc ',
                'abcxyz'  => 'abcxyz',
                ''        => '',
                "\n"      => "\n",
                " \t "    => " \t ",
            ];
        }
        if (self::$meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $valuesExpected = [
                'a B'  => 'a B',
                'zＹ　x' => 'zx',
            ];
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = [
                'abc123'     => 'abc',
                'abc 123'    => 'abc ',
                'abcxyz'     => 'abcxyz',
                'četně'      => 'četně',
                'لعربية'     => 'لعربية',
                'grzegżółka' => 'grzegżółka',
                'België'     => 'België',
                ''           => '',
                "\n"         => "\n",
                " \t "       => " \t ",
            ];
        }

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            self::assertEquals($expected, $actual);
        }
    }

    public function testFilterSupportArray(): void
    {
        $filter = new AlphaFilter();

        $values = [
            'abc123'  => 'abc',
            'abc 123' => 'abc',
            'abcxyz'  => 'abcxyz',
            ''        => '',
        ];

        $actual = $filter->filter(array_keys($values));

        self::assertEquals(array_values($values), $actual);
    }

    /** @return array<array-key, array{0: mixed}> */
    public function returnUnfilteredDataProvider(): array
    {
        return [
            [null],
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     * @param mixed $input
     */
    public function testReturnUnfiltered($input): void
    {
        $filter = new AlphaFilter();

        self::assertEquals($input, $filter->filter($input));
    }
}
