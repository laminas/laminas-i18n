<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Plural as PluralHelper;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress DeprecatedClass, DeprecatedMethod, InternalMethod
 */
class AbstractHelperTest extends TestCase
{
    private PluralHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new PluralHelper();
    }

    public function testHelperHasAbstractHelperInItsHierarchy(): void
    {
        /** @psalm-suppress RedundantCondition */
        self::assertTrue($this->helper instanceof AbstractHelper);
    }

    public function testThatTheViewIsInitiallyNull(): void
    {
        self::assertNull($this->helper->getView());
    }

    public function testThatTheViewCanBeSet(): void
    {
        $renderer = $this->createMock(RendererInterface::class);
        $this->helper->setView($renderer);
        self::assertSame($renderer, $this->helper->getView());
    }
}
