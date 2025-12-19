<?php

declare(strict_types=1);

namespace LaminasTest\i18n\Factory;

use ArrayObject;
use Laminas\I18n\Factory\DefaultLocaleFactory;
use Laminas\ServiceManager\ServiceManager;
use Locale;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_map;

final class DefaultLocaleFactoryTest extends TestCase
{
    /** @return iterable<string, array{0: iterable|null, 1: string}> */
    public static function configScenarios(): iterable
    {
        $system = Locale::getDefault();
        foreach (['en_GB', 'de_DE', 'nl_NL', 'en_US'] as $locale) {
            if ($locale === $system) {
                continue;
            }

            break;
        }

        $values = [
            'No config at all' => [null, $system],
            'Empty config'     => [[], $system],
            'Null value'       => [['locale' => null], $system],
            'Empty string'     => [['locale' => ''], $system],
            'Non-empty'        => [['locale' => $locale], $locale],
        ];

        yield from $values;

        $iterables = array_map(
            static function (array $args): array {
                if ($args[0] === null) {
                    return $args;
                }

                return [
                    new ArrayObject($args[0]),
                    $args[1],
                ];
            },
            $values,
        );

        foreach ($iterables as $label => $args) {
            yield $label . ' (Iterable)' => $args;
        }
    }

    #[DataProvider('configScenarios')]
    public function testALocaleIsReturnedInAllPossibleSituations(iterable|null $config, string $expectLocale): void
    {
        $config = $config === null ? [] : [
            'services' => [
                'config' => $config,
            ],
        ];

        $container = new ServiceManager($config);

        $factory = new DefaultLocaleFactory();
        $result  = $factory->__invoke($container, 'whatever');

        self::assertSame($expectLocale, $result->locale);
        self::assertSame($expectLocale, $result->__toString());
    }
}
