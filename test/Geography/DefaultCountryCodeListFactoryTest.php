<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Geography;

use Laminas\I18n\Geography\DefaultCountryCodeListFactory;
use PHPUnit\Framework\TestCase;

class DefaultCountryCodeListFactoryTest extends TestCase
{
    public function testThatTheFactoryYieldsACountryList(): void
    {
        $this->expectNotToPerformAssertions();
        (new DefaultCountryCodeListFactory())->__invoke();
    }
}
