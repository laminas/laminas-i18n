# Alpha

The `Alpha` filter can be used to **return only alphabetic characters** in the
unicode "letter" category. All other characters are suppressed.

## Basic Usage

```php
$filter = new Laminas\I18n\Filter\Alpha();

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent"
```

By default, if no locale is provided, `Alpha` will use the system locale
provided by PHP's `Locale` class and the `getDefault()` method.

## Using Whitespace

To allow whitespace characters (`\s`) on filtering set the option to `true`;
otherwise they are suppressed.

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\Alpha(true);

echo $filter->filter('This is (my) content: 123'); // "This is my content"
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\Alpha();
$filter->setAllowWhiteSpace(true);

echo $filter->filter('This is (my) content: 123'); // "This is my content"
```

### Get Current Value

To get the current value of this option, use the `getAllowWhiteSpace()` method.

```php
$filter = new Laminas\I18n\Filter\Alpha(true);

$result = $filter->getAllowWhiteSpace(); // true
```

### Default Value

The default value of this option is `false` that means whitespace characters are
suppressed.

## Using Locale

The locale string used in identifying the characters to filter (locale name, 
e.g. `en_US`).

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\Alpha(null, 'en_US');

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent"
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\Alpha();
$filter->setLocale('en_US');

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent"
```

```php fct_label="Locale Class Usage"
Locale::setDefault('en_US');

$filter = new Laminas\I18n\Filter\Alpha();

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent"
```

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$filter = new Laminas\I18n\Filter\Alpha(null, 'en_US');

echo $filter->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `Alpha` will use the system locale
provided by PHP's `Locale::getDefault()`.

## Supported languages

`Alpha` works for most languages, except Chinese, Japanese and Korean. Within
these languages, the English alphabet is used instead of the characters from
these languages. The language itself is detected using PHP's `Locale` class.