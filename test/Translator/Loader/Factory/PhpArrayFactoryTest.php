<?php

declare(strict_types=1);

namespace LaminasTest\i18n\Translator\Loader\Factory;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\Factory\PhpArrayFactory;
use Laminas\I18n\Translator\Loader\PhpArray;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class PhpArrayFactoryTest extends TestCase
{
    public function testNonBooleanIncludePathFlagWillCauseException(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())->method('has')->with('config')->willReturn(true);
        $container->expects($this->once())->method('get')->with('config')->willReturn([
            'laminas-i18n' => [
                'php-loader-options' => [
                    'use-include-path' => 'Nuts',
                ],
            ],
        ]);

        $factory = new PhpArrayFactory();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The PHP array loader include path option must be boolean');

        $factory->__invoke($container, 'foo');
    }

    public function testCanProduceInstanceWithNoConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with('config')->willReturn(false);
        $factory = new PhpArrayFactory();
        self::assertInstanceOf(
            PhpArray::class,
            $factory->__invoke($container, 'foo'),
        );
    }
}
