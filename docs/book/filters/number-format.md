# NumberFormat

The `NumberFormat` filter can be used to **return locale-specific number and
percentage strings**. It extends the [`NumberParse` filter](number-parse.md),
which acts as wrapper for the `NumberFormatter` class within PHP's
internationalization extension (`ext/intl`).

## Basic Usage

```php
$filter = new Laminas\I18n\Filter\NumberFormat();

echo $filter->filter(1234567.8912346); // "1.234.567,891"
```

By default, if no locale is provided, `NumberParse` will use the system locale
provided by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `de_DE`.)

### More Examples

Format a number as percent:

```php
$filter = new Laminas\I18n\Filter\NumberFormat('en_US', NumberFormatter::PERCENT);

echo $filter->filter(0.80); // "80%"
```

Format a number in a scientific format:

```php
$filter = new Laminas\I18n\Filter\NumberFormat('fr_FR', NumberFormatter::SCIENTIFIC);

echo $filter->filter(0.00123456789); // "1,23456789E-3"
```

## Inherited Options and Methods

The `NumberFormat` filter extends the `NumberParse` filter and supports the same
options and methods for locale, style, type and formatter. The descriptions with
examples of usage can be found there:

* [Using Locale](number-parse.md#using-locale)
* [Setting Style](number-parse.md#using-style)
* [Setting Type](number-parse.md#using-type)
* [Setting Custom NumberFormatter](number-parse.md#using-custom-numberformatter)
