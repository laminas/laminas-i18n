<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\Loader\Factory;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\Factory\GettextFactory;
use Laminas\I18n\Translator\Loader\Gettext;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class GettextFactoryTest extends TestCase
{
    public function testNonBooleanIncludePathFlagWillCauseException(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())->method('has')->with('config')->willReturn(true);
        $container->expects($this->once())->method('get')->with('config')->willReturn([
            'laminas-i18n' => [
                'gettext-loader-options' => [
                    'use-include-path' => 'Nuts',
                ],
            ],
        ]);

        $factory = new GettextFactory();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gettext include path option must be boolean');

        $factory->__invoke($container, 'foo');
    }

    public function testCanProduceInstanceWithNoConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with('config')->willReturn(false);
        $factory = new GettextFactory();
        self::assertInstanceOf(
            Gettext::class,
            $factory->__invoke($container, 'foo'),
        );
    }
}
