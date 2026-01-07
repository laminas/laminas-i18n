<?php

declare(strict_types=1);

namespace Laminas\I18n\Factory;

use DateTimeZone;
use Laminas\I18n\CountryCode;
use Laminas\I18n\DefaultLocale;
use Laminas\I18n\I18nDefaults;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Locale;
use NumberFormatter;
use Psr\Container\ContainerInterface;

use function assert;
use function date_default_timezone_get;
use function is_array;
use function is_iterable;
use function is_string;
use function iterator_to_array;
use function preg_match;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal LaminasTest\I18n
 */
final readonly class I18nDefaultsFactory implements FactoryInterface
{
    /** @psalm-suppress MixedAssignment */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): I18nDefaults {
        $locale = $container->get(DefaultLocale::class);

        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        $i18nDefaults = $config['laminas-i18n'] ?? [];
        $i18nDefaults = is_array($i18nDefaults) ? $i18nDefaults : [];

        /**
         * Timezone has historically been at the top-level, but we'll look under the new top-level key too
         */
        $timezone = $config['timezone'] ?? null;
        $timezone = $i18nDefaults['defaultTimeZone'] ?? $timezone;
        $timezone = is_string($timezone) && $timezone !== '' ? $timezone : date_default_timezone_get();

        $currency = $i18nDefaults['defaultCurrency'] ?? null;
        $currency = ! is_string($currency) || $currency === ''
            ? $this->determineCurrencyFromLocale($locale->locale)
            : $currency;

        $country = $i18nDefaults['defaultCountry'] ?? Locale::getRegion($locale->locale);
        assert(is_string($country) && $country !== '');

        $textDomain = $i18nDefaults['defaultTextDomain'] ?? null;
        $textDomain = ! is_string($textDomain) || $textDomain === '' ? 'default' : $textDomain;

        return new I18nDefaults(
            new DateTimeZone($timezone),
            $currency,
            $textDomain,
            $locale->locale,
            CountryCode::fromString($country),
        );
    }

    /** @return non-empty-string */
    private function determineCurrencyFromLocale(string $locale): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatter->setPattern('¤¤');
        $format = $formatter->format(0);
        preg_match('/([A-Z]{3})/', $format, $match);
        $code = $match[1] ?? null;

        assert(is_string($code) && $code !== '');

        return $code;
    }
}
