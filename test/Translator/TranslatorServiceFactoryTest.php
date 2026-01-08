<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator;

use ArrayObject;
use Laminas\I18n\Translator\Loader\PhpMemoryArray;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use LaminasTest\I18n\Translator\TranslationCollector\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslatorServiceFactoryTest extends TestCase
{
    /** @return iterable<string, array{0: iterable, 1: non-empty-string, 2: non-empty-string}> */
    public static function configScenarios(): iterable
    {
        $messages = new PhpMemoryArray([
            'default' => [
                'en_GB' => [
                    'Message 1' => 'Message 1 en_GB',
                ],
                'de_DE' => [
                    'Message 1' => 'Message 1 de_DE',
                    'DE Only'   => 'DE Only Message',
                ],
            ],
        ]);

        $scenarios = [
            'No config'               => [[], 'Foo', 'Foo'],
            'Default locale is used'  => [
                [
                    'locale'             => 'en_GB',
                    'laminas-i18n'       => [
                        'translator' => [
                            'remote_translation' => [
                                ['type' => 'RemoteService'],
                            ],
                        ],
                    ],
                    'translator_plugins' => [
                        'services' => [
                            'RemoteService' => $messages,
                        ],
                    ],
                ],
                'Message 1',
                'Message 1 en_GB',
            ],
            'Fallback locale is used' => [
                [
                    'locale'             => 'en_GB',
                    'laminas-i18n'       => [
                        'translator' => [
                            'remote_translation' => [
                                ['type' => 'RemoteService'],
                            ],
                            'fallback_locale'    => 'de_DE',
                        ],
                    ],
                    'translator_plugins' => [
                        'services' => [
                            'RemoteService' => $messages,
                        ],
                    ],
                ],
                'DE Only',
                'DE Only Message',
            ],
        ];

        yield from $scenarios;

        foreach ($scenarios as $key => $args) {
            yield $key . ' (Object)' => [
                new ArrayObject($args[0]),
                $args[1],
                $args[2],
            ];
        }
    }

    /** @param non-empty-string $messageKey */
    #[DataProvider('configScenarios')]
    public function testTranslatorIsProduced(iterable $config, string $messageKey, string $expect): void
    {
        $container = TestHelper::containerWithConfig($config);
        $factory   = new TranslatorServiceFactory();

        $translator = $factory->__invoke($container, 'foo');

        self::assertSame($expect, $translator->translate($messageKey));
    }
}
