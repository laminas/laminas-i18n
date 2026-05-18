<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector\Factory;

use ArrayObject;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TranslationCollector\Factory\FilePatternCollectorFactory;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FilePatternCollectorFactoryTest extends TestCase
{
    /** @return iterable<string, array{0: iterable, 1: string, 2: string|null}> */
    public static function configScenarios(): iterable
    {
        $scenarios = [
            'No Config'               => [[], 'Message', null],
            'No Translator Config'    => [['laminas-i18n' => []], 'Message', null],
            'No File List Config'     => [['laminas-i18n' => ['translator' => []]], 'Message', null],
            'Empty File List'         => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => [],
                        ],
                    ],
                ],
                'Message',
                null,
            ],
            'Invalid File List Entry' => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => [
                                'Not an array specification - will be ignored',
                            ],
                        ],
                    ],
                ],
                'Message',
                null,
            ],
            'Valid File List Entry'   => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => [
                                [
                                    'type'     => PhpArray::class,
                                    'base_dir' => __DIR__ . '/../translations',
                                    'pattern'  => 'array-%s.php',
                                ],
                            ],
                        ],
                    ],
                ],
                'Message',
                'Message (en)',
            ],
            'Non default text domain' => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'translation_file_patterns' => [
                                [
                                    'type'        => PhpArray::class,
                                    'base_dir'    => __DIR__ . '/../translations',
                                    'pattern'     => 'array-%s.php',
                                    'text_domain' => 'foo',
                                ],
                            ],
                        ],
                    ],
                ],
                'Message',
                null,
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

        $collector = (new FilePatternCollectorFactory())->__invoke($container, 'foo');

        self::assertSame($expect, $collector->collect('default', 'en_GB')[$messageKey]);
    }
}
