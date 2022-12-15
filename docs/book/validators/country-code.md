# CountryCode

`Laminas\I18n\Validator\CountryCode` allows you to validate if a given value is
**known and valid** ISO-3166 alpha 2 country code.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\CountryCode();

if ($validator->isValid('FR')) {
    // Value is a valid country code
}
```
