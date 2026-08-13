# Alnum

`Laminas\I18n\Validator\Alnum` allows you to validate if a given value
**contains only alphabetical characters and digits**.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\Alnum();

if ($validator->isValid('Abcd12')) {
    // Value contains only allowed chars
}
```

## Using Whitespace

By default, whitespace is not accepted as it is not part of the alphabet.
However, if you want to validate complete sentences or phrases, you may need to
allow whitespace; this can be done via the `allowWhiteSpace` option, either at
instantiation or afterwards via the `setAllowWhiteSpace()` method.

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\Alnum(['allowWhiteSpace' => true]);

    if ($validator->isValid('Abcd and 12')) {
        // Value contains only allowed chars
    }
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\Alnum();
    $validator->setAllowWhiteSpace(true);

    if ($validator->isValid('Abcd and 12')) {
        // Value contains only allowed chars
    }
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `getAllowWhiteSpace()` method.

```php
$validator = new Laminas\I18n\Validator\Alnum(['allowWhiteSpace' => true]);

$result = $validator->getAllowWhiteSpace(); // true
```

### Default Value

The default value of this option is `false` that means whitespace characters are
not allowed.

## Using different Languages

Several languages supported by PHP's internationalization extension (`ext/intl`)
use alphabets where characters are formed from multiple bytes, including
*Korean*, *Japanese*, and *Chinese*. Such languages therefore are unsupported
with regards to the `Alnum` validator.

When using the `Alnum` validator with these languages, the input will be
validated using the English alphabet.
