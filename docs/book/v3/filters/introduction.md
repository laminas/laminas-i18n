# Introduction

The `laminas-i18n` ecosystem provides a set of internationalization-related filters to sanitize and normalize localized data:

- [Alnum](alnum.md)
- [Alpha](alpha.md)
- [NumberFormat](number-format.md)
- [NumberParse](number-parse.md)

These filters are designed to work seamlessly with the [laminas-filter](https://docs.laminas.dev/laminas-filter/) component pipeline. For general concepts, workflows, and basic usage instructions regarding filters, please refer to the [laminas-filter documentation](https://docs.laminas.dev/laminas-filter/).

> MISSING: **Installation Requirements**
> Starting with version 3.0, the core `laminas-i18n` package no longer ships with these filters out of the box.
> They have been extracted into an optional satellite package.
>
> To use internationalization filters in an application, the satellite package must be installed, which will automatically bring in `laminas-filter` as a required dependency:
>
> ```bash
> $ composer require laminas/laminas-i18n-filter
> ```
