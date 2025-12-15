# Introduction

## Translation

`Laminas\I18n` comes with a [complete translation](translation.md) suite which supports all major
formats and includes popular features like plural translations and text domains.
The Translator component is mostly dependency free, except for the fallback to a
default locale, where it relies on the `intl` PHP extension.

The translator itself is initialized without any parameters, as any configuration
to it is optional. A translator without any translations will actually do nothing
but just return the given message IDs.

## Filters and Validators

The `Laminas\I18n` component also provides a [set of filters](filters/introduction.md) for normalizing and formatting.

The component also provides a [set of validators](validators/introduction.md) for validating localized data.

## View Helpers

The `Laminas\I18n` component provides also a [set of view helpers](view-helpers/introduction.md) for formatting dates, times, numbers, currencies, and translating messages.

## Based on PHP's `intl` extension

The [`intl` extension](https://www.php.net/manual/book.intl.php) is a wrapper for the ICU library, which provides a lot of
internationalization functions. The `intl` extension is a standard PHP extension, and is required for the `Laminas\I18n` component to work.

The `intl` functions are used for locale detection, filtering and validating localized data, formatting localized data and more.
These functions are used in:

- Translator
- Filters
- Validators
- View Helpers
