<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\Escaper\Escaper;
use Laminas\I18n\CountryCode;
use Laminas\I18n\Geography\CountryCodeListInterface;
use Laminas\View\HtmlAttributesSet;
use Locale;

use function array_map;
use function implode;
use function iterator_to_array;
use function sprintf;

use const PHP_EOL;

/**
 * A View Helper that outputs an HTML datalist of all the ISO-3166 countries
 */
final class CountryCodeDataList
{
    public function __construct(
        private CountryCodeListInterface $codeList,
        private Escaper $escaper,
        private string $defaultLocale
    ) {
    }

    /**
     * @param string|null $locale Must be a valid locale string - used to format the names of the countries
     * @param array<string, scalar> $dataListAttributes An array of HTML attributes applied to the datalist element
     */
    public function __invoke(?string $locale = null, array $dataListAttributes = []): string
    {
        $locale = $locale ?? $this->defaultLocale;
        $list   = array_map(function (CountryCode $code) use ($locale): string {
            $name = Locale::getDisplayRegion('-' . $code->toString(), $locale);
            return sprintf(
                '<option value="%s" label="%s">',
                $code->toString(),
                $this->escaper->escapeHtmlAttr($name)
            );
        }, iterator_to_array($this->codeList));

        $attributes = new HtmlAttributesSet($this->escaper, $dataListAttributes);

        return sprintf(
            '<datalist %s>%s</datalist>',
            $attributes->__toString(),
            implode(PHP_EOL, $list)
        );
    }
}
