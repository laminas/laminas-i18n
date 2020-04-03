# Alpha

`Laminas\I18n\Validator\Alpha` allows you to validate if a given value
**contains only alphabetical characters**. This validator is identical to the
[`Laminas\I18n\Validator\Alnum` validator](alnum.md) with the exception that it
does not accept digits.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\Alpha();

if ($validator->isValid('Abcd')) {
    // Value contains only allowed chars
}
```

## Using Whitespace

By default, whitespace is not accepted as it is not part of the alphabet.
However, if you want to validate complete sentences or phrases, you may need to
allow whitespace; this can be done via the `allowWhiteSpace` option, either at
instantiation or afterwards via the `setAllowWhiteSpace()` method.

=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\Alpha(['allowWhiteSpace' => true]);
    
    if ($validator->isValid('Abcd and efg')) {
        // Value contains only allowed chars
    }
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\Alpha();
    $validator->setAllowWhiteSpace(true);
    
    if ($validator->isValid('Abcd and efg')) {
        // Value contains only allowed chars
    }
    ```

### Get Current Value

To get the current value of this option, use the `getAllowWhiteSpace()` method.

```php
$validator = new Laminas\I18n\Validator\Alpha(['allowWhiteSpace' => true]);

$result = $validator->getAllowWhiteSpace(); // true
```

### Default Value

The default value of this option is `false` that means whitespace characters are
not allowed.

## Using different Languages

When using `Laminas\I18n\Validator\Alpha`, the language provided by the user's
browser will be used to set the allowed characters. For locales outside of
English, this means that additional alphabetic characters may be used
&mdash; such as `ä`, `ö` and `ü` from the German alphabet.

Which characters are allowed depends completely on the language, as every
language defines its own set of characters.

Three languages supported by PHP's internationalization extension (`ext/intl`),
however, define multibyte characters, which cannot be matched as alphabetic
characters using normal string or regular expression options. These include
*Korean*, *Japanese*, and *Chinese*.

As a result, when using the `Alpha` validator with these languages, the input
will be validated using the English alphabet.
