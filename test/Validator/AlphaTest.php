<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Alpha as AlphaValidator;
use LaminasTest\I18n\TestCase;

class AlphaTest extends TestCase
{
    /** @var AlphaValidator */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new AlphaValidator();
    }

    /**
     * Ensures that the validator follows expected behavior
     */
    public function testBasic(): void
    {
        $valuesExpected = [
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false,
        ];
        foreach ($valuesExpected as $input => $result) {
            self::assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     */
    public function testGetMessages(): void
    {
        self::assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     */
    public function testAllowWhiteSpace(): void
    {
        $this->validator->setAllowWhiteSpace(true);

        $valuesExpected = [
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            "a\tb c"  => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            self::assertEquals(
                $result,
                $this->validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
            );
        }
    }

    public function testNonStringValidation(): void
    {
        self::assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testEqualsMessageTemplates(): void
    {
        $validator = $this->validator;

        self::assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }
}
