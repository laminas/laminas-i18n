<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;
use LaminasTest\I18n\TestCase;

class TextDomainTest extends TestCase
{
    public function testInstantiation(): void
    {
        $domain = new TextDomain(['foo' => 'bar']);
        self::assertEquals('bar', $domain['foo']);
    }

    public function testArrayAccess(): void
    {
        $domain        = new TextDomain();
        $domain['foo'] = 'bar';
        self::assertEquals('bar', $domain['foo']);
    }

    public function testPluralRuleSetter(): void
    {
        $domain = new TextDomain();
        $domain->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));
        self::assertEquals(2, $domain->getPluralRule()->evaluate(2));
    }

    public function testPluralRuleDefault(): void
    {
        $domain = new TextDomain();
        self::assertEquals(1, $domain->getPluralRule()->evaluate(0));
        self::assertEquals(0, $domain->getPluralRule()->evaluate(1));
        self::assertEquals(1, $domain->getPluralRule()->evaluate(2));
    }

    public function testMerging(): void
    {
        $domainA = new TextDomain(['foo' => 'bar', 'bar' => 'baz']);
        $domainB = new TextDomain(['baz' => 'bat', 'bar' => 'bat']);
        $domainA->merge($domainB);

        self::assertEquals('bar', $domainA['foo']);
        self::assertEquals('bat', $domainA['bar']);
        self::assertEquals('bat', $domainA['baz']);
    }

    public function testMergingIncompatibleTextDomains(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not compatible');

        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainA->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));
        $domainB->setPluralRule(PluralRule::fromString('nplurals=2; plural=n'));

        $domainA->merge($domainB);
    }

    public function testMergingTextDomainsWithPluralRules(): void
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();

        $domainA->merge($domainB);
        self::assertFalse($domainA->hasPluralRule());
        self::assertFalse($domainB->hasPluralRule());
    }

    public function testMergingTextDomainWithPluralRuleIntoTextDomainWithoutPluralRule(): void
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainB->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));

        $domainA->merge($domainB);
        self::assertEquals(3, $domainA->getPluralRule()->getNumPlurals());
        self::assertEquals(3, $domainB->getPluralRule()->getNumPlurals());
    }

    public function testMergingTextDomainWithoutPluralRuleIntoTextDomainWithPluralRule(): void
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainA->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));

        $domainA->merge($domainB);
        self::assertEquals(3, $domainA->getPluralRule()->getNumPlurals());
        self::assertFalse($domainB->hasPluralRule());
    }
}
