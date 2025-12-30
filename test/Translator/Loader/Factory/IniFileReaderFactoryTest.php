<?php

declare(strict_types=1);

namespace LaminasTest\i18n\Translator\Loader\Factory;

use ArrayObject;
use Laminas\I18n\ConfigProvider;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Loader\Factory\IniFileReaderFactory;
use Laminas\I18n\Translator\Loader\IniFileReader;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function is_array;
use function iterator_to_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class IniFileReaderFactoryTest extends TestCase
{
    public function testIniReaderCanBeProducedWithDefaultConfig(): void
    {
        $factory = new IniFileReaderFactory();

        $reader = $factory->__invoke($this->containerWithConfig(), 'foo');
        self::assertInstanceOf(IniFileReader::class, $reader);
    }

    /** @return iterable<string, array{0: iterable<string, mixed>}> */
    public static function invalidConfiguration(): iterable
    {
        $cases = [
            'Separator non string'   => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'nest-separator' => 1,
                        ],
                    ],
                ],
            ],
            'Separator empty string' => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'nest-separator' => '',
                        ],
                    ],
                ],
            ],
            'Sections not boolean'   => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'process-sections' => 'Fred',
                        ],
                    ],
                ],
            ],
            'Typed not boolean'      => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'typed' => 'Fred',
                        ],
                    ],
                ],
            ],
        ];

        yield from $cases;

        foreach ($cases as $key => $case) {
            yield $key . ' (As Object)' => [new ArrayObject($case[0])];
        }
    }

    #[DataProvider('invalidConfiguration')]
    public function testExceptionThrownForVariousInvalidConfigurationValues(iterable $config): void
    {
        $factory = new IniFileReaderFactory();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid option configuration for ini file reader');
        $factory->__invoke($this->containerWithConfig($config), 'foo');
    }

    /** @return iterable<string, array{0: iterable<string, mixed>}> */
    public static function validConfiguration(): iterable
    {
        $cases = [
            'Empty Config'          => [
                [],
            ],
            'Empty Top Level'       => [
                [
                    'laminas-i18n' => [],
                ],
            ],
            'Empty Options'         => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [],
                    ],
                ],
            ],
            'Typed non default'     => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'typed' => true,
                        ],
                    ],
                ],
            ],
            'Sections non default'  => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'process-sections' => false,
                        ],
                    ],
                ],
            ],
            'Separator non default' => [
                [
                    'laminas-i18n' => [
                        'ini-format-options' => [
                            'nest-separator' => '|',
                        ],
                    ],
                ],
            ],
        ];

        yield from $cases;

        foreach ($cases as $key => $case) {
            yield $key . ' (As Object)' => [new ArrayObject($case[0])];
        }
    }

    #[DataProvider('validConfiguration')]
    public function testReaderIsProducedWithVariousConfigValues(iterable $config): void
    {
        $this->expectNotToPerformAssertions();
        $factory = new IniFileReaderFactory();
        $factory->__invoke($this->containerWithConfig($config), 'foo');
    }

    private function containerWithConfig(iterable $config = []): ContainerInterface
    {
        $useObject = ! is_array($config);

        $config = iterator_to_array($config);
        $config = array_replace_recursive(
            (new ConfigProvider())->__invoke(),
            $config,
        );

        $config = array_replace_recursive(
            $config,
            [
                'dependencies' => [
                    'services' => [
                        'config' => $useObject ? new ArrayObject($config) : $config,
                    ],
                ],
            ],
        );

        /** @psalm-var ServiceManagerConfiguration $dependencies */
        $dependencies = $config['dependencies'];

        return new ServiceManager($dependencies);
    }
}
