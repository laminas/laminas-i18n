<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Alnum as AlnumValidator;
use LaminasTest\I18n\TestCase;

class AlnumTest extends TestCase
{
    /** @var AlnumValidator */
    protected $validator;

    /**
     * Creates a new Alnum object for each test method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new AlnumValidator();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values
     */
    public function testExpectedResultsWithBasicInputValues(): void
    {
        $valuesExpected = [
            'abc123'  => true,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false,
            'foobar1' => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value
     */
    public function testMessagesEmptyInitially(): void
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues(): void
    {
        $this->validator->setAllowWhiteSpace(true);

        $valuesExpected = [
            'abc123'  => true,
            'abc 123' => true,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            'foobar1' => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
            );
        }
    }

    public function testEmptyStringValueResultsInProperValidationFailureMessages(): void
    {
        $this->assertFalse($this->validator->isValid(''));

        $messages      = $this->validator->getMessages();
        $arrayExpected = [
            AlnumValidator::STRING_EMPTY => 'The input is an empty string',
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    public function testInvalidValueResultsInProperValidationFailureMessages(): void
    {
        $this->assertFalse($this->validator->isValid('#'));
        $messages      = $this->validator->getMessages();
        $arrayExpected = [
            AlnumValidator::NOT_ALNUM => 'The input contains characters which are non alphabetic and no digits',
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    public function testNonStringValidation(): void
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testIntegerValidation(): void
    {
        $this->assertTrue($this->validator->isValid(1));
    }

    public function testEqualsMessageTemplates(): void
    {
        $validator = $this->validator;

        $this->assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }
}
