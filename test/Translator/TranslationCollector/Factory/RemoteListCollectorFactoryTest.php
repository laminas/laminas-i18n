<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector\Factory;

use ArrayObject;
use Laminas\I18n\Exception\InvalidArgumentException;
use Laminas\I18n\Translator\Loader\PhpMemoryArray;
use Laminas\I18n\Translator\TranslationCollector\Factory\RemoteListCollectorFactory;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RemoteListCollectorFactoryTest extends TestCase
{
    /** @return iterable<string, array{0: iterable, 1: string, 2: string|null}> */
    public static function configScenarios(): iterable
    {
        $loader = new PhpMemoryArray([
            'default' => [
                'en_GB' => [
                    'Message' => 'Translation',
                ],
            ],
        ]);

        $scenarios = [
            'No Config'               => [[], 'Message', null],
            'No Translator Config'    => [['laminas-i18n' => []], 'Message', null],
            'No File List Config'     => [['laminas-i18n' => ['translator' => []]], 'Message', null],
            'Empty List'              => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'remote_translation' => [],
                        ],
                    ],
                ],
                'Message',
                null,
            ],
            'Invalid List Entry'      => [
                [
                    'laminas-i18n' => [
                        'translator' => [
                            'remote_translation' => [
                                'Not an array specification - will be ignored',
                            ],
                        ],
                    ],
                ],
                'Message',
                null,
            ],
            'Valid Entry'             => [
                [
                    'laminas-i18n'       => [
                        'translator' => [
                            'remote_translation' => [
                                [
                                    'type'        => 'SomeLoader',
                                    'text_domain' => 'default',
                                ],
                            ],
                        ],
                    ],
                    'translator_plugins' => [
                        'services' => [
                            'SomeLoader' => $loader,
                        ],
                    ],
                ],
                'Message',
                'Translation',
            ],
            'Non default text domain' => [
                [
                    'laminas-i18n'       => [
                        'translator' => [
                            'remote_translation' => [
                                [
                                    'type'        => 'SomeLoader',
                                    'text_domain' => 'other-domain',
                                ],
                            ],
                        ],
                    ],
                    'translator_plugins' => [
                        'services' => [
                            'SomeLoader' => $loader,
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

        $collector = (new RemoteListCollectorFactory())->__invoke($container, 'foo');

        self::assertSame($expect, $collector->collect('default', 'en_GB')[$messageKey]);
    }

    /** @return iterable<string, array{0: array, 1: string}> */
    public static function invalidConfig(): iterable
    {
        yield 'Entry with missing type' => [
            [
                'laminas-i18n' => [
                    'translator' => [
                        'remote_translation' => [
                            [
                                'text_domain' => 'something',
                            ],
                        ],
                    ],
                ],
            ],
            'The `type` key for each remote loader must be a string',
        ];

        yield 'Entry with non-string type' => [
            [
                'laminas-i18n' => [
                    'translator' => [
                        'remote_translation' => [
                            [
                                'type'        => 1,
                                'text_domain' => 'something',
                            ],
                        ],
                    ],
                ],
            ],
            'The `type` key for each remote loader must be a string',
        ];

        yield 'Entry with empty string type' => [
            [
                'laminas-i18n' => [
                    'translator' => [
                        'remote_translation' => [
                            [
                                'type'        => '',
                                'text_domain' => 'something',
                            ],
                        ],
                    ],
                ],
            ],
            'The `type` key for each remote loader must be a string',
        ];

        yield 'Entry with non-string text_domain' => [
            [
                'laminas-i18n' => [
                    'translator' => [
                        'remote_translation' => [
                            [
                                'type'        => 'foo',
                                'text_domain' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'The `text_domain` key must resolve to a non-empty-string',
        ];

        yield 'Entry with empty string text_domain' => [
            [
                'laminas-i18n' => [
                    'translator' => [
                        'remote_translation' => [
                            [
                                'type'        => 'foo',
                                'text_domain' => '',
                            ],
                        ],
                    ],
                ],
            ],
            'The `text_domain` key must resolve to a non-empty-string',
        ];
    }

    #[DataProvider('invalidConfig')]
    public function testInvalidSpecifications(array $config, string $expectMessage): void
    {
        $container = TestHelper::containerWithConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectMessage);

        (new RemoteListCollectorFactory())->__invoke($container, 'foo');
    }
}
