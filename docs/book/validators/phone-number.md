# PhoneNumber

`Laminas\I18n\Validator\PhoneNumber` allows you to determine if a given value
**is a valid phone number**. Phone numbers are specific to country codes.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\PhoneNumber();

var_dump($validator->isValid('+4930123456')); // true
```

By default, if no country code is provided, `PhoneNumber` will use the system
locale provided by PHP's `Locale::getDefault()` and `Locale::getRegion()` to
extract the country code.

(The above example assumes that the environment locale is set to `de_DE`.)

## Using Country

The ISO 3611 country code can be set for validations.

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber(['country' => 'DE']);

    var_dump($validator->isValid('+4930123456')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber();
    $validator->setCountry('DE');

    var_dump($validator->isValid('+4930123456')); // true
    ```

=== "Locale Class Usage"
    ```php
    Locale::setDefault('de_DE');

    $validator = new Laminas\I18n\Validator\PhoneNumber();

    var_dump($validator->isValid('+4930123456')); // true
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `getCountry()` method.

```php
$validator = new Laminas\I18n\Validator\PhoneNumber(['country' => 'US']);

echo $validator->getCountry(); // 'US'
```

### Default Value

By default, if no country is provided, `PhoneNumber` will use the system locale
provided by PHP's `Locale::getDefault()` and `Locale::getRegion()` to extract
the region code.

## Using Allowed Phone Number Patterns

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber([
        'allowed_types' => ['emergency'],
        'country'       => 'US',
    ]);

    var_dump($validator->isValid(911)); // true
    var_dump($validator->isValid(999)); // false
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber();
    $validator->allowedTypes(['emergency']);
    $validator->setCountry('US');

    var_dump($validator->isValid(911)); // true
    var_dump($validator->isValid(999)); // false
    ```
<!-- markdownlint-restore -->

Possible values for allowed patterns are:

- `emergency`
- `fixed`
- `general`
- `mobile`
- `pager`
- `personal`
- `premium`
- `shared`
- `shortcode`
- `tollfree`
- `uan`
- `voicemail`
- `voip`

NOTE: **All Allowed Patterns**
The complete list of allowed patterns is not available for each country code.
Please check the file for your country code with the supported types in the [laminas-i18n repository on GitHub](https://github.com/laminas/laminas-i18n/tree/master/src/Validator/PhoneNumber) or in the `vendor/laminas/laminas-i18n/src/Validator/PhoneNumber` directory of your project folder.

### Get Current Value

To get the current value of this option, use the `allowedTypes()` method with
the value `null`.

```php
$validator = new Laminas\I18n\Validator\PhoneNumber(['allowed_types' => ['emergency']]);

var_dump($validator->allowedTypes()); // ['emergency']
```

### Default Value

The following phone number patterns are allowed per default:

- `fixed`
- `general`
- `mobile`
- `personal`
- `tollfree`
- `uan`
- `voip`

## Strict Validation

By default, the phone numbers are validated against strict number patterns. To
allow validation with all _possible_ phone numbers, the `allow_possible` option
can be used.

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber([
        'allow_possible' => true,
        'allowed_types'  => ['emergency'],
        'country'        => 'US',
    ]);

    var_dump($validator->isValid(911)); // true
    var_dump($validator->isValid(999)); // true
    var_dump($validator->isValid(9999)); // false
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\PhoneNumber();
    $validator->allowPossible(true);
    $validator->allowedTypes(['emergency']);
    $validator->setCountry('US');

    var_dump($validator->isValid(911)); // true
    var_dump($validator->isValid(999)); // true
    var_dump($validator->isValid(9999)); // false
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `allowPossible()` method with
the value `null`.

```php
$validator = new Laminas\I18n\Validator\PhoneNumber(['allow_possible' => true]);

var_dump($validator->allowPossible()); // true
```

### Default Value

The default value of this option is `false`.

## Specify Country Code on Validation

The country code can be specified with the `context` parameter on the `isValid`
method. This allows to validate phone numbers for different country codes with
the same validator instance without the usage of the `setCountry()` method.

```php
$validator = new Laminas\I18n\Validator\PhoneNumber([
    'country' => 'country-code', // Defines a placeholder
]);

var_dump($validator->isValid('+37067811268', ['country-code' => 'LT'])); // true
var_dump($validator->isValid('+37067811268', ['country-code' => 'DE'])); // false
var_dump($validator->isValid('+37067811268', ['country-code' => 'US'])); // false
```
