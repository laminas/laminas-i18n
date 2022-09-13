<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Validator;

use Generator;
use Laminas\I18n\Validator\PostCode as PostCodeValidator;
use Laminas\Validator\Exception\InvalidArgumentException;
use LaminasTest\I18n\TestCase;

class PostCodeTest extends TestCase
{
    private PostCodeValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PostCodeValidator(['locale' => 'de_AT']);
    }

    /**
     * @dataProvider UKPostCodesDataProvider
     */
    public function testUKBasic(string $postCode, bool $expected): void
    {
        $ukValidator = new PostCodeValidator(['locale' => 'en_GB']);
        self::assertSame($expected, $ukValidator->isValid($postCode));
    }

    /** @return array<array-key, array{0: string, 1: bool}> */
    public function UKPostCodesDataProvider(): array
    {
        return [
            ['CA3 5JQ', true],
            ['GL15 2GB', true],
            ['GL152GB', true],
            ['ECA32 6JQ', false],
            ['se5 0eg', false],
            ['SE5 0EG', true],
            ['ECA3 5JQ', false],
            ['WC2H 7LTa', false],
            ['WC2H 7LTA', false],
        ];
    }

    /** @return array<array-key, array{0: mixed, 1: bool}> */
    public function postCodesDataProvider(): array
    {
        return [
            ['2292',    true],
            ['1000',    true],
            ['0000',    true],
            ['12345',   false],
            [1234,      true],
            [9821,      true],
            ['21A4',    false],
            ['ABCD',    false],
            [true,      false],
            ['AT-2292', false],
            [1.56,      false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider postCodesDataProvider
     * @param mixed $postCode
     */
    public function testBasic($postCode, bool $expected): void
    {
        self::assertEquals($expected, $this->validator->isValid($postCode));
    }

    /**
     * Ensures that getMessages() returns expected default value
     */
    public function testGetMessages(): void
    {
        self::assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that a region is available
     */
    public function testSettingLocalesWithoutRegion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Locale must contain a region');
        $this->validator->setLocale('de')->isValid('1000');
    }

    /**
     * Ensures that the region contains postal codes
     */
    public function testSettingLocalesWithoutPostalCodes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A postcode-format string has to be given for validation');
        $this->validator->setLocale('gez_ER')->isValid('1000');
    }

    /**
     * Ensures locales can be retrieved
     */
    public function testGettingLocale(): void
    {
        self::assertEquals('de_AT', $this->validator->getLocale());
    }

    /**
     * Ensures format can be set and retrieved
     */
    public function testSetGetFormat(): void
    {
        $this->validator->setFormat('\d{1}');
        self::assertEquals('\d{1}', $this->validator->getFormat());
    }

    public function testSetGetFormatThrowsExceptionOnNullFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A postcode-format string has to be given');
        $this->validator->setLocale(null)->setFormat(null)->isValid('1000');
    }

    public function testSetGetFormatThrowsExceptionOnEmptyFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A postcode-format string has to be given');
        $this->validator->setLocale(null)->setFormat('')->isValid('1000');
    }

    public function testErrorMessageText(): void
    {
        self::assertFalse($this->validator->isValid('hello'));
        $message = $this->validator->getMessages();
        self::assertStringContainsString('not appear to be a postal code', $message['postcodeNoMatch']);
    }

     /**
      * Test service class with invalid validation
      */
    public function testServiceClass(): void
    {
        $params = (object) [
            'serviceTrue'  => null,
            'serviceFalse' => null,
        ];

        $serviceTrue = static function (string $value) use ($params): bool {
            $params->serviceTrue = $value;
            return true;
        };

        $serviceFalse = static function (string $value) use ($params): bool {
            $params->serviceFalse = $value;
            return false;
        };

        self::assertEquals(null, $this->validator->getService());

        $this->validator->setService($serviceTrue);
        self::assertEquals($this->validator->getService(), $serviceTrue);
        self::assertTrue($this->validator->isValid('2292'));
        self::assertEquals($params->serviceTrue, '2292');

        $this->validator->setService($serviceFalse);
        self::assertEquals($this->validator->getService(), $serviceFalse);
        self::assertFalse($this->validator->isValid('hello'));
        self::assertEquals($params->serviceFalse, 'hello');

        $message = $this->validator->getMessages();
        self::assertStringContainsString('not appear to be a postal code', $message['postcodeService']);
    }

    public function testEqualsMessageTemplates(): void
    {
        $validator = $this->validator;
        self::assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }

    /**
     * Post codes are provided by French government official post code database
     * https://www.data.gouv.fr/fr/datasets/base-officielle-des-codes-postaux/
     */
    public function testFrPostCodes(): void
    {
        $validator = $this->validator;
        $validator->setLocale('fr_FR');

        self::assertTrue($validator->isValid('13100')); // AIX EN PROVENCE
        self::assertTrue($validator->isValid('97439')); // STE ROSE
        self::assertTrue($validator->isValid('98790')); // MAHETIKA
        self::assertFalse($validator->isValid('00000')); // Post codes starting with 00 don't exist
        self::assertFalse($validator->isValid('96000')); // Post codes starting with 96 don't exist
        self::assertFalse($validator->isValid('99000')); // Post codes starting with 99 don't exist
    }

    /**
     * Post codes are provided by Norway Mail database
     * http://www.bring.no/hele-bring/produkter-og-tjenester/brev-og-postreklame/andre-tjenester/postnummertabeller
     */
    public function testNoPostCodes(): void
    {
        $validator = $this->validator;
        $validator->setLocale('en_NO');

        self::assertTrue($validator->isValid('0301')); // OSLO
        self::assertTrue($validator->isValid('9910')); // BJØRNEVATN
        self::assertFalse($validator->isValid('0000')); // Postal code 0000
    }

    /**
     * Postal codes in Latvia are 4 digit numeric and use a mandatory ISO 3166-1 alpha-2 country code (LV) in front,
     * i.e. the format is “LV-NNNN”.
     * To prevent BC break LV- prefix is optional
     * https://en.wikipedia.org/wiki/Postal_codes_in_Latvia
     */
    public function testLvPostCodes(): void
    {
        $validator = $this->validator;
        $validator->setLocale('en_LV');

        self::assertTrue($validator->isValid('LV-0000'));
        self::assertTrue($validator->isValid('0000'));
        self::assertFalse($validator->isValid('ABCD'));
        self::assertFalse($validator->isValid('LV-ABCD'));
    }

    /** @return Generator<string, array{0: int}> */
    public function liPostCode(): Generator
    {
        yield 'Nendeln' => [9485];
        yield 'Schaanwald' => [9486];
        yield 'Gamprin-Bendern' => [9487];
        yield 'Schellenberg' => [9488];
        yield 'Vaduz-9489' => [9489];
        yield 'Vaduz-9490' => [9490];
        yield 'Ruggell' => [9491];
        yield 'Eschen' => [9492];
        yield 'Mauren' => [9493];
        yield 'Schaan' => [9494];
        yield 'Triesen' => [9495];
        yield 'Balzers' => [9496];
        yield 'Triesenberg' => [9497];
        yield 'Planken' => [9498];
    }

    /**
     * @dataProvider liPostCode
     */
    public function testLiPostCodes(int $postCode): void
    {
        $validator = $this->validator;
        $validator->setLocale('de_LI');

        self::assertTrue($validator->isValid($postCode));
    }
}
