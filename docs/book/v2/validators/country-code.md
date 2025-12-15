# CountryCode

`Laminas\I18n\Validator\CountryCode` allows you to validate if a given value is **known and valid** two-letter country codes of [ISO-3166 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2).

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\CountryCode();

if ($validator->isValid('FR')) {
    // Value is a valid country code
}
```
