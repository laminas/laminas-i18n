<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\I18n\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use stdClass;
use Throwable;

use function class_exists;
use function method_exists;

final class LoaderPluginManagerCompatibilityTest extends TestCase
{
    /** @param class-string $expected */
    #[DataProvider('aliasProvider')]
    public function testPluginAliasesResolve(string $alias, string $expected): void
    {
        $this->assertInstanceOf($expected, $this->getPluginManager()->get($alias), "Alias '$alias' does not resolve'");
    }

    protected static function getPluginManager(): LoaderPluginManager
    {
        return new LoaderPluginManager(new ServiceManager());
    }

    /** @return class-string<Throwable> */
    protected function getV2InvalidPluginException(): string
    {
        return RuntimeException::class;
    }

    public function testShareByDefaultAndSharedByDefault(): void
    {
        $manager        = $this->getPluginManager();
        $reflection     = new ReflectionClass($manager);
        $shareByDefault = $sharedByDefault = true;

        foreach ($reflection->getProperties() as $prop) {
            if ($prop->getName() === 'shareByDefault') {
                /** @var mixed $shareByDefault */
                $shareByDefault = $prop->getValue($manager);
                self::assertIsBool($shareByDefault);
            }
            if ($prop->getName() === 'sharedByDefault') {
                /** @var mixed $sharedByDefault */
                $sharedByDefault = $prop->getValue($manager);
                self::assertIsBool($sharedByDefault);
            }
        }

        $this->assertSame(
            $shareByDefault,
            $sharedByDefault,
            'Values of shareByDefault and sharedByDefault do not match'
        );
    }

    public function testRegisteringInvalidElementRaisesException(): void
    {
        $this->expectException($this->getServiceNotFoundException());
        /** @psalm-suppress InvalidArgument */
        $this->getPluginManager()->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException(): void
    {
        $manager = $this->getPluginManager();
        $manager->setInvokableClass('test', stdClass::class);
        $this->expectException($this->getServiceNotFoundException());
        $manager->get('test');
    }

    /** @return list<array{0: string, 1: class-string}> */
    public static function aliasProvider(): array
    {
        $manager         = self::getPluginManager();
        $reflectionClass = new ReflectionClass($manager);
        $constant        = $reflectionClass->getReflectionConstant('CONFIGURATION');

        /** @psalm-var mixed $config */
        $config = $constant->getValue();
        self::assertIsArray($config);
        self::assertArrayHasKey('aliases', $config);
        self::assertIsArray($config['aliases']);

        $data = [];
        foreach ($config['aliases'] as $alias => $expected) {
            self::assertIsString($alias);
            self::assertIsString($expected);
            self::assertTrue(class_exists($expected));
            $data[] = [$alias, $expected];
        }

        return $data;
    }

    /** @return class-string<Throwable> */
    protected function getServiceNotFoundException(): string
    {
        $manager = $this->getPluginManager();
        if (method_exists($manager, 'configure')) {
            return InvalidServiceException::class;
        }
        return $this->getV2InvalidPluginException();
    }
}
