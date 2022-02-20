<?php

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Alpha as AlphaValidator;
use LaminasTest\I18n\TestCase;

/**
 * @group      Laminas_Validator
 */
class AlphaTest extends TestCase
{
    /**
     * @var AlphaValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new AlphaValidator();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
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
            "\n"      => false
            ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testAllowWhiteSpace()
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
            "a\tb c"  => true
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
            );
        }
    }

    /**
     * @Laminas-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;

        $this->assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }
}
