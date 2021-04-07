# IsFloat

`Laminas\I18n\Validator\IsFloat` allows you to validate if a given value
**contains a floating-point value**. This validator takes into account localized
input.

Float values are often written differently based on the country or region. For
example, using English, you might write `1.5`, whereas in german you would write
`1,5`, and in other languages you might use grouping.

`Laminas\I18n\Validator\IsFloat` is able to validate such notations. However, it
is limited to the locale you set.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\IsFloat();

$validator->isValid(1234.5);    // true
$validator->isValid('10a01');   // false
$validator->isValid('1,234.5'); // true
```

By default, if no locale is provided, `IsFloat` will use the system locale
provided by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `en`.)

Using a notation not specific to the locale results in a `false` evaluation.

## Using Locale

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\IsFloat(['locale' => 'en_US']);

    $validator->isValid(1234.5); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\IsFloat();
    $validator->setLocale('en_US');

    $validator->isValid(1234.5); // true
    ```

=== "Locale Class Usage"
    ```php
    Locale::setDefault('en_US');

    $validator = new Laminas\I18n\Validator\IsFloat();

    $validator->isValid(1234.5); // true
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$validator = new Laminas\I18n\Validator\IsFloat(['locale' => 'en_US']);

echo $validator->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `IsFloat` will use the system locale
provided by PHP's `Locale::getDefault()`.
