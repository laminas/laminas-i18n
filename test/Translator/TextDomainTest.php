<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;
use PHPUnit_Framework_TestCase as TestCase;

class TextDomainTest extends TestCase
{
    public function testInstantiation()
    {
        $domain = new TextDomain(array('foo' => 'bar'));
        $this->assertEquals('bar', $domain['foo']);
    }

    public function testArrayAccess()
    {
        $domain = new TextDomain();
        $domain['foo'] = 'bar';
        $this->assertEquals('bar', $domain['foo']);
    }

    public function testPluralRuleSetter()
    {
        $domain = new TextDomain();
        $domain->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));
        $this->assertEquals(2, $domain->getPluralRule()->evaluate(2));
    }

    public function testPluralRuleDefault()
    {
        $domain = new TextDomain();
        $this->assertEquals(1, $domain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $domain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $domain->getPluralRule()->evaluate(2));
    }

    public function testMerging()
    {
        $domainA = new TextDomain(array('foo' => 'bar', 'bar' => 'baz'));
        $domainB = new TextDomain(array('baz' => 'bat', 'bar' => 'bat'));
        $domainA->merge($domainB);

        $this->assertEquals('bar', $domainA['foo']);
        $this->assertEquals('bat', $domainA['bar']);
        $this->assertEquals('bat', $domainA['baz']);
    }

    public function testMergingIncompatibleTextDomains()
    {
        $this->setExpectedException('Laminas\I18n\Exception\RuntimeException', 'is not compatible');

        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainA->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));
        $domainB->setPluralRule(PluralRule::fromString('nplurals=2; plural=n'));

        $domainA->merge($domainB);
    }

    public function testMergingTextDomainsWithPluralRules()
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();

        $domainA->merge($domainB);
        $this->assertFalse($domainA->hasPluralRule());
        $this->assertFalse($domainB->hasPluralRule());
    }

    public function testMergingTextDomainWithPluralRuleIntoTextDomainWithoutPluralRule()
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainB->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));

        $domainA->merge($domainB);
        $this->assertEquals(3, $domainA->getPluralRule()->getNumPlurals());
        $this->assertEquals(3, $domainB->getPluralRule()->getNumPlurals());
    }

    public function testMergingTextDomainWithoutPluralRuleIntoTextDomainWithPluralRule()
    {
        $domainA = new TextDomain();
        $domainB = new TextDomain();
        $domainA->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));

        $domainA->merge($domainB);
        $this->assertEquals(3, $domainA->getPluralRule()->getNumPlurals());
        $this->assertFalse($domainB->hasPluralRule());
    }
}
