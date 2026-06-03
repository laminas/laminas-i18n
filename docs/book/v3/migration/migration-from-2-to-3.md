# Migration from Versions 2.x to 3.0

## Removed Dependencies

### `laminas/laminas-config`

The dependency on `laminas/laminas-config` has been removed. The component now utilizes a built-in INI file reader implementation instead.

## Removed Classes

### `Laminas\I18n\Validator\PhoneNumber`

The deprecated phone number validator has been removed in favour of the standalone library [`laminas-i18n-phone-number`](https://github.com/laminas/laminas-i18n-phone-number).

In most cases, the phone number library can be used as a direct replacement by using `Laminas\I18n\PhoneNumber\Validator\PhoneNumber`.

## Backward-Incompatible Changes

### `Laminas\I18n\Translator\Loader\Ini` Initialization

Due to the removal of `laminas-config`, the `Ini` translator loader no longer implicitly instantiates an internal configuration reader. It now requires `Laminas\I18n\Translator\Loader\IniFileReader` passed explicitly to its constructor.

> [!NOTE] Service Container Notice
> If your application resolves the `Ini` translation loader via a service container (such as `Laminas\ServiceManager`), this instantiation change is automatically handled by the component's internal factories. Manual intervention is only required if you instantiate the loader directly in your code.

#### Prior to 3.0.0

```php
use Laminas\I18n\Translator\Loader\Ini as IniLoader;

$loader = new IniLoader();

```

#### Since 3.0.0

```php
use Laminas\I18n\Translator\Loader\Ini as IniLoader;
use Laminas\I18n\Translator\Loader\IniFileReader;

$loader = new IniLoader(new IniFileReader());

```

## Behaviour Changes

The deprecated, static constructor `Laminas\I18n\Exception\InvalidArgumentException::withUnknownCountryCode()` has been removed.
