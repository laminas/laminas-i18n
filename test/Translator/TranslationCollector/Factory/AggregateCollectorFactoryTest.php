<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector\Factory;

use ArrayObject;
use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TranslationCollector\Factory\AggregateCollectorFactory;
use Laminas\I18n\Translator\TranslationCollector\FileListCollector;
use Laminas\I18n\Translator\TranslationCollector\FilePatternCollector;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AggregateCollectorFactoryTest extends TestCase
{
    /** @return iterable<string, array{0: iterable, 1: string, 2: string|null}> */
    public static function configScenarios(): iterable
    {
        $patternConfig = [
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/../translations/more-translations',
                'pattern'  => 'array-%s.php',
            ],
        ];

        $fileConfig = [
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/../translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
        ];

        $scenarios = [
            'No Config'                                  => [
                [],
                'Message',
                null,
            ],
            'No Translator Config'                       => [
                ['laminas-i18n' => []],
                'Message',
                null,
            ],
            'No Aggregate Config'                        => [
                ['laminas-i18n' => ['translator' => []]],
                'Message',
                null,
            ],
            'No Aggregate Config, Collectors Configured' => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => $patternConfig,
                            'translation_files'         => $fileConfig,
                        ],
                    ],
                ],
                'Message',
                'Message (en)',
            ],
            'Pattern First'                              => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => $patternConfig,
                            'translation_files'         => $fileConfig,
                            'aggregate_collector'       => [
                                FilePatternCollector::class,
                                FileListCollector::class,
                            ],
                        ],
                    ],
                ],
                'Message',
                'Message (en)',
            ],
            'List First'                                 => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => $patternConfig,
                            'translation_files'         => $fileConfig,
                            'aggregate_collector'       => [
                                FileListCollector::class,
                                FilePatternCollector::class,
                            ],
                        ],
                    ],
                ],
                'Message',
                'Another Message (en)',
            ],
        ];

        yield from $scenarios;

        foreach ($scenarios as $key => $args) {
            yield $key . ' (Object)' => [new ArrayObject($args[0]), $args[1], $args[2]];
        }
    }

    #[DataProvider('configScenarios')]
    public function testCollectorIsProduced(iterable $config, string $messageKey, string|null $expect): void
    {
        $container = TestHelper::containerWithConfig($config);

        $collector = (new AggregateCollectorFactory())->__invoke($container, 'foo');

        self::assertSame($expect, $collector->collect('default', 'en_GB')[$messageKey]);
    }

    public function testConfiguringANonStringCollectorIsExceptional(): void
    {
        $container = TestHelper::containerWithConfig([
            'laminas-i18n' => [
                'translator' => [
                    'aggregate_collector' => [
                        123,
                    ],
                ],
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each item in the `aggregate_collector` list must be a string');

        (new AggregateCollectorFactory())->__invoke($container, 'foo');
    }
}
