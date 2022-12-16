# Introduction

laminas-i18n ships with a set of laminas-view helper classes related to
internationalization.

- [CountryCodeDataList](country-code-data-list.md)
- [CurrencyFormat](currency-format.md)
- [DateFormat](date-format.md)
- [NumberFormat](number-format.md)
- [Plural](plural.md)
- [Translate](translate.md)
- [TranslatePlural](translate-plural.md)

These helpers are based on Laminas component for the view layer:
[laminas-view](https://docs.laminas.dev/laminas-view/) and their
[helpers](https://docs.laminas.dev/laminas-view/helpers/intro/).

> MISSING: **Installation Requirements**
> The view-helper support of laminas-i18n depends on the [laminas-view](https://docs.laminas.dev/laminas-view/) component, so be sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-view
> ```

---

<!-- markdownlint-disable MD001 -->
> TIP: **IDE Auto-Completion in Templates**
> The `Laminas\I18n\View\HelperTrait` trait can be used to provide auto-completion for modern IDEs. It defines the aliases of the view helpers in a DocBlock as `@method` tags.
>
> ### Usage
>
> In order to allow auto-completion in templates, `$this` variable should be type-hinted via a DocBlock at the top of a template.
> It is recommended that> always the `Laminas\View\Renderer\PhpRenderer` is added as the first type, so that the IDE can auto-suggest the default view helpers from `laminas-view`.
> The `HelperTrait` from `laminas-i18n` can be chained with a pipe symbol (a.k.a. vertical bar) `|`:
>
> ```php
> /**
>  * @var Laminas\View\Renderer\PhpRenderer|Laminas\I18n\View\HelperTrait $this
>  */
> ```
>
> The `HelperTrait` traits can be chained as many as needed, depending on which view helpers from the different Laminas component are used and where the auto-completion is to be made.
<!-- markdownlint-restore -->

## Abstract Translator Helper

The `AbstractTranslatorHelper` view helper is used as a base abstract class for
any helpers that need to translate content. It provides an implementation for
the `Laminas\I18n\Translator\TranslatorAwareInterface`, allowing translator
injection as well as text domain injection.

### Public Methods

#### setTranslator()

```php
setTranslator(Translator $translator, string $textDomain = null) : void
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
