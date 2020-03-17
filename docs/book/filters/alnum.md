# Alnum

The `Alnum` filter can be used to **return only alphabetic characters and 
digits** in the unicode "letter" and "number" categories, respectively. All 
other characters are suppressed.

## Basic Usage

```php
$filter = new Laminas\I18n\Filter\Alnum();

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent123"
```

By default, if no locale is provided, `Alnum` will use the system locale
provided by PHP's `Locale` class and the `getDefault()` method.

## Using Whitespace

To allow whitespace characters (`\s`) on filtering set the option to `true`;
otherwise they are suppressed.

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\Alnum(true);

echo $filter->filter('This is (my) content: 123'); // "This is my content 123"
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\Alnum();
$filter->setAllowWhiteSpace(true);

echo $filter->filter('This is (my) content: 123'); // "This is my content 123"
```

### Get Current Value

To get the current value of this option, use the `getAllowWhiteSpace()` method.

```php
$filter = new Laminas\I18n\Filter\Alnum(true);

$result = $filter->getAllowWhiteSpace(); // true
```

### Default Value

The default value of this option is `false` that means whitespace characters are
suppressed.

## Using Locale

The locale string used in identifying the characters to filter (locale name, 
e.g. `en_US`).

```php fct_label="Constructor Usage"
$filter = new Laminas\I18n\Filter\Alnum(null, 'en_US');

echo $filter->filter("This is (my) content: 123"); // "Thisismycontent123"
```

```php fct_label="Setter Usage"
$filter = new Laminas\I18n\Filter\Alnum();
$filter->setLocale('en_US');

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent123"
```

```php fct_label="Locale Class Usage"
Locale::setDefault('en_US');

$filter = new Laminas\I18n\Filter\Alnum();

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent123"
```

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$filter = new Laminas\I18n\Filter\Alnum(null, 'en_US');

echo $filter->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `Alnum` will use the system locale
provided by PHP's `Locale::getDefault()`.

## Supported Languages

`Alnum` works for most languages, except *Korean*, *Japanese*, and *Chinese*.
Within these languages, the English alphabet is used instead of the characters
from these languages. The language itself is detected using PHP's `Locale`
class.
