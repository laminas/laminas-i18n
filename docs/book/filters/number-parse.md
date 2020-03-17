# NumberParse

The `NumberParse` filter can be used to **parse a number from a string**. It 
acts as a wrapper for the `NumberFormatter` class within PHP's
internationalization extension (`ext/intl`).

## Basic Usage

```php
$filter = new Laminas\I18n\Filter\NumberParse();

echo $filter->filter('1.234.567,891'); // 1234567.8912346
```

By default, if no locale is provided, `NumberParse` will use the system locale
provided by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `de_DE`.)

## Using Locale

The locale string used in identifying the characters to filter (locale name, 
e.g. `en_US` or `de_DE`).

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\NumberParse('de_DE');

echo $filter->filter('1.234.567,891'); // 1234567.8912346
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\NumberParse();
$filter->setLocale('de_DE');

echo $filter->filter('1.234.567,891'); // 1234567.8912346
```

```php fct_label="Locale Class Usage"
Locale::setDefault('de_DE');

$filter = new Laminas\I18n\Filter\NumberParse();

echo $filter->filter('1.234.567,891'); // 1234567.8912346
```

> ### Notice
>
> After the first filtering, the locale changes will have no effect anymore.
> Create a new instance of the filter to change the locale.

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$filter = new Laminas\I18n\Filter\NumberParse('en_US');

echo $filter->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `NumberParse` will use the system locale
provide by PHP's `Locale::getDefault()`.

## Using Style

This option sets the style of the parsing; one of the 
[`NumberFormatter` format style constants](http://www.php.net/manual/class.numberformatter.php#intl.numberformatter-constants.unumberformatstyle).

```php fct_label="Constructor Usage"
// Example 1
$filter = new Laminas\I18n\Filter\NumberParse('en_US', NumberFormatter::PERCENT);

echo $filter->filter('80%'); // 0.80

// Example 2
$filter = new Laminas\I18n\Filter\NumberParse('fr_FR', NumberFormatter::SCIENTIFIC);

echo $filter->filter('1,23456789E-3'); // 0.00123456789
```

```php fct_label="Setter Usage"
// Example 1
$filter = new Laminas\I18n\Filter\NumberParse('en_US');
$filter->setStyle(NumberFormatter::PERCENT);

echo $filter->filter('80%'); // 0.80

// Example 2
$filter = new Laminas\I18n\Filter\NumberParse('fr_FR');
$filter->setStyle(NumberFormatter::SCIENTIFIC);

echo $filter->filter('1,23456789E-3'); // 0.00123456789
```

> ### Notice
>
> After the first filtering, the style changes will have no effect anymore. 
> Create a new instance of the filter to change the style.

### Get Current Value

To get the current value of this option, use the `getStyle()` method.

```php
$filter = new Laminas\I18n\Filter\NumberParse();

echo $filter->getStyle(); // 1 (NumberFormatter::DEFAULT_STYLE)
```

### Default Value

The default value of this option is `NumberFormatter::DEFAULT_STYLE`.

## Using Type

The type speficied the [`NumberFormatter` parsing type](http://www.php.net/manual/class.numberformatter.php#intl.numberformatter-constants.types)
to use.

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\NumberParse(
    'de_DE',
    NumberFormatter::DEFAULT_STYLE,
    NumberFormatter::DECIMAL
);

echo $filter->filter('1.234.567,891'); // 1234567
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\NumberParse();
$filter->setLocale('de_DE');
$filter->setType(NumberFormatter::DECIMAL);

echo $filter->filter('1.234.567,891'); // 1234567
```

### Get Current Value

To get the current value of this option, use the `getType()` method.

```php
$filter = new Laminas\I18n\Filter\NumberParse();

echo $filter->getType(); // 3 (NumberFormatter::TYPE_DOUBLE)
```

### Default Value

The default value of this option is `NumberFormatter::TYPE_DOUBLE`.

## Using Custom NumberFormatter

```php
$formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT); 
$filter    = new Laminas\I18n\Filter\NumberParse();
$filter->setFormatter($formatter);
```

> ### Notice
>
> If a custom formatter is set, the locale and / or the style changes will
> have no effect anymore. Set a new number formatter to change the locale and /
> or the style.

### Get Current Value

To get the current value of this option, use the `getFormatter()` method.

```php
$filter = new Laminas\I18n\Filter\NumberParse();

$formatter = $filter->getFormatter(); // instance of `NumberFormatter`
```

### Default Value

The default value of this option is an instance of PHP's `NumberFormatter` class.
Created with the current values for locale and style of the filter.
