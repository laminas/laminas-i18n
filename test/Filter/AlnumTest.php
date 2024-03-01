<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Filter;

use Laminas\I18n\Filter\Alnum as AlnumFilter;
use LaminasTest\I18n\TestCase;
use Locale;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

use function array_keys;
use function array_values;
use function preg_match;

class AlnumTest extends TestCase
{
    private AlnumFilter $filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     */
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
        $this->filter               = new AlnumFilter();
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
                'abc123'  => 'abc123',
                'abc 123' => 'abc123',
                'abcxyz'  => 'abcxyz',
                'AZ@#4.3' => 'AZ43',
                ''        => '',
            ];
        } elseif (self::$meansEnglishAlphabet) {
            // The Alphabet means english alphabet.

            /**
             * The first element contains multibyte alphabets and digits.
             *  But , AlnumFilter is expected to return only singlebyte alphabets and digits.
             *
             * The second contains multibyte or singebyte space.
             * The third  contains various multibyte or singebyte characters.
             */
            $valuesExpected = [
                'aＡBｂ3４5６'    => 'aB35',
                'z７ Ｙ8　x９'    => 'z8x',
                '，s1.2r３#:q,' => 's12rq',
            ];
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = [
                'abc123'       => 'abc123',
                'abc 123'      => 'abc123',
                'abcxyz'       => 'abcxyz',
                'če2t3ně'      => 'če2t3ně',
                'grz5e4gżółka' => 'grz5e4gżółka',
                'Be3l5gië'     => 'Be3l5gië',
                ''             => '',
            ];
        }

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            self::assertIsString($actual);
            self::assertEquals($expected, $actual);
        }
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     */
    public function testAllowWhiteSpace(): void
    {
        $this->filter->setAllowWhiteSpace(true);
        self::assertTrue($this->filter->getAllowWhiteSpace());

        if (! self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = [
                'abc123'  => 'abc123',
                'abc 123' => 'abc 123',
                'abcxyz'  => 'abcxyz',
                'AZ@#4.3' => 'AZ43',
                ''        => '',
                "\n"      => "\n",
                " \t "    => " \t ",
            ];
        } elseif (self::$meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $valuesExpected = [
                'a B ４5' => 'a B 5',
                'z3　x'   => 'z3x',
            ];
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = [
                'abc123'        => 'abc123',
                'abc 123'       => 'abc 123',
                'abcxyz'        => 'abcxyz',
                'če2 t3ně'      => 'če2 t3ně',
                'gr z5e4gżółka' => 'gr z5e4gżółka',
                'Be3l5 gië'     => 'Be3l5 gië',
                ''              => '',
            ];
        }

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            self::assertIsString($actual);
            self::assertEquals($expected, $actual);
        }
    }

    public function testFilterSupportArray(): void
    {
        $filter = new AlnumFilter();

        $values = [
            'abc123'  => 'abc123',
            'abc 123' => 'abc123',
            'abcxyz'  => 'abcxyz',
            'AZ@#4.3' => 'AZ43',
            ''        => '',
        ];

        $actual = $filter->filter(array_keys($values));
        self::assertIsArray($actual);

        self::assertEquals(array_values($values), $actual);
    }

    /** @return array<array-key, array{0: mixed}> */
    public static function returnUnfilteredDataProvider(): array
    {
        return [
            [null],
            [new stdClass()],
        ];
    }

    /**
     * @param mixed $input
     */
    #[DataProvider('returnUnfilteredDataProvider')]
    public function testReturnUnfiltered($input): void
    {
        $filter = new AlnumFilter();

        self::assertEquals($input, $filter->filter($input));
    }
}
