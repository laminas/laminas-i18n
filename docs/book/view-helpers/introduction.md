# Introduction

laminas-i18n ships with a set of laminas-view helper classes related to
internationalization.

* [CurrencyFormat](currency-format.md)
* [DateFormat](date-format.md)
* [NumberFormat](number-format.md)
* [Plural](plural.md)
* [Translate](translate.md)
* [TranslatePlural](translate-plural.md)

These helpers are based on Laminas component for the view layer:
[laminas-view](https://docs.laminas.dev/laminas-view/) and their
[helpers](https://docs.laminas.dev/laminas-view/helpers/intro/).

> ### Installation Requirements
>
> The view-helper support of laminas-i18n depends on the
> [laminas-view](https://docs.laminas.dev/laminas-view/) component, so be sure
> to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-view
> ```


## Abstract Translator Helper

The `AbstractTranslatorHelper` view helper is used as a base abstract class for
any helpers that need to translate content. It provides an implementation for
the `Laminas\I18n\Translator\TranslatorAwareInterface`, allowing translator
injection as well as text domain injection.

### Public Methods

#### setTranslator()

```php
setTranslator(
    Translator $translator [ ,
    string $textDomain = null
] ) : void
```

Sets the `Laminas\I18n\Translator\Translator` instance to use in the helper. The
`$textDomain` argument is optional, and provided as a convenienct to allow
setting both the translator and text domain simultaneously.

#### getTranslator()

```php
getTranslator() : Translator
```

Returns the `Laminas\I18n\Translator\Translator` instance used by the helper.

#### hasTranslator()

```php
hasTranslator() : bool
```

Returns true if the helper composes a `Laminas\I18n\Translator\Translator`
instance.

#### setTranslatorEnabled()

```php
setTranslatorEnabled(bool $enabled) : void
```

Sets whether or not translations are enabled.

#### isTranslatorEnabled()

```php
isTranslatorEnabled() : bool
```

Returns true if translations are enabled.

#### setTranslatorTextDomain()

```php
setTranslatorTextDomain(string $textDomain) : void
```

Sets the default translation text domain to use with the helper.

#### getTranslatorTextDomain()

```php
getTranslatorTextDomain() : string
```

Returns the current text domain used by the helper.
