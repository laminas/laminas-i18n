<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\IsInt as IsIntValidator;
use Laminas\Validator\Exception;
use LaminasTest\I18n\TestCase;
use Locale;
use PHPUnit\Framework\Attributes\DataProvider;

class IsIntTest extends TestCase
{
    private IsIntValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new IsIntValidator();
    }

    /** @return array<array-key, array{0: mixed, 1: bool}> */
    public static function intDataProvider(): array
    {
        return [
            [1.00,         true],
            [0.00,         true],
            [0.01,         false],
            [-0.1,         false],
            [-1,           true],
            ['10',         true],
            [1,            true],
            ['not an int', false],
            [true,         false],
            [false,        false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $intVal
     */
    #[DataProvider('intDataProvider')]
    public function testBasic($intVal, bool $expected): void
    {
        $this->validator->setLocale('en');
        self::assertEquals($expected, $this->validator->isValid($intVal));
    }

    /**
     * Ensures that getMessages() returns expected default value
     */
    public function testGetMessages(): void
    {
        self::assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales(): void
    {
        $this->validator->setLocale('de');
        self::assertEquals('de', $this->validator->getLocale());
        self::assertEquals(false, $this->validator->isValid('10 000'));
        self::assertEquals(true, $this->validator->isValid('10.000'));
    }

    public function testNonStringValidation(): void
    {
        self::assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testUsingApplicationLocale(): void
    {
        Locale::setDefault('de');
        $valid = new IsIntValidator();
        self::assertTrue($valid->isValid('10.000'));
    }

    public function testLocaleDetectsNoEnglishLocaleOnOtherSetLocale(): void
    {
        Locale::setDefault('de');
        $valid = new IsIntValidator();
        self::assertTrue($valid->isValid(1200));
        self::assertFalse($valid->isValid('1,200'));
    }

    public function testEqualsMessageTemplates(): void
    {
        $validator = $this->validator;

        self::assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }

    public function testGetStrict(): void
    {
        self::assertFalse(
            $this->validator->getStrict()
        );

        $this->validator->setStrict(true);
        self::assertTrue(
            $this->validator->getStrict()
        );
    }

    /**
     * @return array<array-key, array{0: mixed}>
     */
    public static function setStrictInvalidParameterDataProvider(): array
    {
        return [
            [null],
            ['true'],
            ['1'],
            ['1.0'],
            ['false'],
            ['0'],
            ['0.0'],
        ];
    }

    /**
     * @param mixed $strict
     */
    #[DataProvider('setStrictInvalidParameterDataProvider')]
    public function testSetStrictThrowsInvalidArgumentException($strict): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        /** @psalm-suppress MixedArgument */
        $this->validator->setStrict($strict);
    }

    /**
     * @return array<array-key, array{0: mixed, 1: bool}>
     */
    public static function strictIntDataProvider(): array
    {
        return [
            [1,            true],
            [0,            true],
            [1.00,         false],
            [0.00,         false],
            [0.01,         false],
            [-0.1,         false],
            [-1,           true],
            ['10',         false],
            ['1',          false],
            ['not an int', false],
            [true,         false],
            [false,        false],
        ];
    }

    /**
     * @param mixed $intVal
     */
    #[DataProvider('strictIntDataProvider')]
    public function testStrictComparison($intVal, bool $expected): void
    {
        $this->validator->setLocale('en');
        $this->validator->setStrict(true);

        self::assertSame($expected, $this->validator->isValid($intVal));
    }
}
