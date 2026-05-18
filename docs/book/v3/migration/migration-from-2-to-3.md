# Migration from Versions 2.x to 3.0

## Removed Classes

### `Laminas\I18n\Validator\PhoneNumber`

The deprecated phone number validator has been removed in favour of the standalone library [`laminas-i18n-phone-number`](https://github.com/laminas/laminas-i18n-phone-number).

In most cases, the phone number library can be used as a direct replacement by using `Laminas\I18n\PhoneNumber\Validator\PhoneNumber`

## Behaviour Changes

The deprecated, static constructor `Laminas\I18n\Exception\InvalidArgumentException::withUnknownCountryCode()` has been removed.
