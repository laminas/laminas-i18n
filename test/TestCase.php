<?php

declare(strict_types=1);

namespace LaminasTest\I18n;

use Locale;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.AbstractClass.Prefix
abstract class TestCase extends PHPUnitTestCase
{
    private string $defaultLocale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultLocale = Locale::getDefault();
        self::assertNotNull($this->defaultLocale);
    }

    protected function tearDown(): void
    {
        Locale::setDefault($this->defaultLocale);
        parent::tearDown();
    }
}
