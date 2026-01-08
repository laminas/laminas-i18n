<?php

declare(strict_types=1);

namespace LaminasTest\I18n\Translator\TranslationCollector;

use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\TranslationCollector\AggregateCollector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AggregateCollectorTest extends TestCase
{
    public function testAnEmptyAggregateWillYieldEmptyMessages(): void
    {
        $collector = new AggregateCollector([]);
        $messages  = $collector->collect('default', 'en_GB');
        self::assertCount(0, $messages);
    }

    /** @return iterable<string, array{0: AggregateCollector, 1: string, 2: string|null}> */
    public static function orderedCollectors(): iterable
    {
        $list = TestHelper::fileListCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'filename' => __DIR__ . '/translations/array-en_GB.php',
                'locale'   => 'en_GB',
            ],
        ]);

        $pattern = TestHelper::filePatternCollectorWithConfig([
            [
                'type'     => PhpArray::class,
                'base_dir' => __DIR__ . '/translations/more-translations',
                'pattern'  => 'array-%s.php',
            ],
        ]);

        yield 'List First' => [
            new AggregateCollector([$list, $pattern]),
            'Message',
            'Another Message (en)',
        ];

        yield 'Pattern First' => [
            new AggregateCollector([$pattern, $list]),
            'Message',
            'Message (en)',
        ];
    }

    #[DataProvider('orderedCollectors')]
    public function testCollectorResultsAreMergedSequentially(
        AggregateCollector $collector,
        string $messageKey,
        string|null $expect,
    ): void {
        $messages = $collector->collect('default', 'en_GB');

        self::assertSame($expect, $messages[$messageKey]);
    }
}
