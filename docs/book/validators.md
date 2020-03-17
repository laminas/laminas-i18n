# Validators

laminas-i18n provides a set of validators that use internationalization
capabilities.

## Alnum

`Laminas\I18n\Validator\Alnum` allows you to validate if a given value contains
only alphabetical characters and digits. There is no length limitation for the
input you want to validate.

### Supported options

The following options are supported for `Laminas\I18n\Validator\Alnum`:

- `allowWhiteSpace`: Whether or not whitespace characters are allowed. This
  option defaults to `FALSE`.

### Basic usage

```php
$validator = new Laminas\I18n\Validator\Alnum();
if ($validator->isValid('Abcd12')) {
    // value contains only allowed chars
} else {
    // false
}
```

### Using whitespace

By default, whitespace is not accepted as it is not part of the alphabet.
However, if you want to validate complete sentences or phrases, you may need to
allow whitespace; this can be done via the `allowWhiteSpace` option, either at
instantiation or afterwards via the `setAllowWhiteSpace()` method.

To get the current state of the flag, use the `getAllowWhiteSpace()` method.

```php
$validator = new Laminas\I18n\Validator\Alnum(['allowWhiteSpace' => true]);

// or set it via method call:
$validator->setAllowWhiteSpace(true);

if ($validator->isValid('Abcd and 12')) {
    // value contains only allowed chars
} else {
    // false
}
```

### Using different languages

Several languages supported by ext/intl use alphabets where characters are
formed from multiple bytes, including *Korean*, *Japanese*, and *Chinese*. Such
languages therefore are unsupported with regards to the `Alnum` validator.

When using the `Alnum` validator with these languages, the input will be validated
using the English alphabet.
