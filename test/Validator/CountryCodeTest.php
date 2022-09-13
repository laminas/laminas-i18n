<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use ArrayObject;
use Laminas\I18n\CountryCode;
use Laminas\I18n\Validator\CountryCode as CountryCodeValidator;
use PHPUnit\Framework\TestCase;

use function sprintf;

class CountryCodeTest extends TestCase
{
    private CountryCodeValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new CountryCodeValidator();
    }

    public function testACountryCodeValueObjectIsConsideredValid(): void
    {
        self::assertTrue(
            $this->validator->isValid(CountryCode::fromString('ZA'))
        );
    }

    public function testAKnownCountryCodeStringIsConsideredValid(): void
    {
        self::assertTrue(
            $this->validator->isValid('ZW')
        );
    }

    public function testAKnownLowerCaseCountryCodeStringIsConsideredValid(): void
    {
        self::assertTrue(
            $this->validator->isValid('us')
        );
    }

    /** @return list<array{0: string}> */
    public function invalidStringProvider(): array
    {
        return [
            ['Foo'],
            ['a'],
            ['ZZ'],
            ['Something Else'],
        ];
    }

    /** @dataProvider invalidStringProvider */
    public function testInvalidStrings(string $value): void
    {
        self::assertFalse(
            $this->validator->isValid($value)
        );

        $messages = $this->validator->getMessages();

        self::assertArrayHasKey(
            CountryCodeValidator::INVALID,
            $messages
        );

        self::assertStringContainsString(
            sprintf('"%s"', $value),
            $messages[CountryCodeValidator::INVALID]
        );
    }

    /** @return list<array{0: mixed}> */
    public function mixedDataProvider(): array
    {
        return [
            [null],
            [1],
            [1.2],
            [['GB']],
            [new ArrayObject()],
        ];
    }

    /** @dataProvider mixedDataProvider */
    public function testNonStringsAreNotAcceptable(mixed $value): void
    {
        self::assertFalse(
            $this->validator->isValid($value)
        );

        $messages = $this->validator->getMessages();

        self::assertArrayHasKey(
            CountryCodeValidator::NOT_STRING,
            $messages
        );
    }

    public function testEmptyStringsAreNotAcceptable(): void
    {
        self::assertFalse(
            $this->validator->isValid('')
        );

        $messages = $this->validator->getMessages();

        self::assertArrayHasKey(
            CountryCodeValidator::EMPTY_STRING,
            $messages
        );
    }
}
