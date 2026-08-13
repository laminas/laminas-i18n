# Introduction

The `laminas-i18n` ecosystem provides a robust set of validators that leverage internationalization capabilities to sanitize and validate localized input data:

- [Alnum](alnum.md)
- [Alpha](alpha.md)
- [CountryCode](country-code.md)
- [DateTime](date-time.md)
- [IsFloat](is-float.md)
- [IsInt](is-int.md)
- [PhoneNumber](phone-number.md)
- [PostCode](post-code.md)

These validators extend and integrate with the core validation component
pipeline: [laminas-validator](https://docs.laminas.dev/laminas-validator/). For
information on standard validation workflows or error message localization,
please consult the [laminas-validator documentation](https://docs.laminas.dev/laminas-validator/intro/#translating-messages).

> MISSING: **Installation Requirements**
> Starting with version 3.0, the core `laminas-i18n` package no longer ships with these validators built-in.
> They have been extracted into an optional satellite package to keep core component dependencies lightweight.
>
> To use internationalization validators in an application, the satellite package must be installed, which will automatically bring in `laminas-validator` as a required dependency:
>
> ```bash
> $ composer require laminas/laminas-i18n-validator
> ```
