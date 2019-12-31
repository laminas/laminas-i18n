# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.7.4 - 2017-05-17

### Added

- Nothing.

### Changes

- [zendframework/zend-i18n#65](https://github.com/zendframework/zend-i18n/pull/65) updates the
  `PostCode` validation for Ireland to support Eircode
  (https://www.eircode.ie/what-is-eircode)

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#74](https://github.com/zendframework/zend-i18n/pull/74) fixes how the
  `LoaderPluginManagerFactory` factory initializes the plugin manager instance,
  ensuring it is injecting the relevant configuration from the `config` service
  and thus seeding it with configured translator loader services. This means
  that the `translator_plugins` configuration will now be honored in
  non-laminas-mvc contexts.
- [zendframework/zend-i18n#56](https://github.com/zendframework/zend-i18n/pull/56) adds more aliases to
  the `LoaderPluginManager` to ensure different cAsIng strategies will still
  resolve translation loaders under laminas-servicemanager v3.
- [zendframework/zend-i18n#62](https://github.com/zendframework/zend-i18n/pull/62) fixes an issue with
  how the gettext adapter resolves `PoEdit` source keywords when a text_domain is
  defined.
- [zendframework/zend-i18n#73](https://github.com/zendframework/zend-i18n/pull/73) provides a
  workaround within the `CurrencyFormat` view helper for an ICU bug
  (http://bugs.icu-project.org/trac/ticket/10997).

## 2.7.3 - 2016-06-07

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#42](https://github.com/zendframework/zend-i18n/pull/42) fixes the
  behavior of the `PhoneNumber` validator to store the country using the casing
  provided, but validate based on the uppercased country value. This ensures
  the same validation behavior, and prevents the value from being transformed,
  potentially breaking later retrieval.
- [zendframework/zend-i18n#47](https://github.com/zendframework/zend-i18n/pull/47) provides a
  performance improvement to the `Laminas\I18n\View\HelperConfig` implementation
  when operating under laminas-servicemanager v3.

## 2.7.2 - 2016-04-18

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#45](https://github.com/zendframework/zend-i18n/pull/45) fixes the
  `Module::init()` method to properly receive a `ModuleManager` instance, and
  not expect a `ModuleEvent`.

## 2.7.1 - 2016-03-30

### Added

- [zendframework/zend-i18n#41](https://github.com/zendframework/zend-i18n/pull/41) adds
  `Laminas\I18n\Module::init()`, which registers a specification for the translator
  loader plugin manager with `Laminas\ModuleManager\Listener\ServiceListener`.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.7.0 - 2016-03-30

### Added

- [zendframework/zend-i18n#40](https://github.com/zendframework/zend-i18n/pull/40) adds:
  - `Laminas\I18n\Translator\LoaderPluginManagerFactory`, which provides a factory
    for container-interop-compatible containers (including laminas-servicemanager)
    for creating and returning a `LoaderPluginManager` instance.
  - `Laminas\I18n\ConfigProvider` (which provides an invokable configuration
    provider class; this could be used with mezzio) and
    `Laminas\I18n\Module` (which provides a laminas-mvc/laminas-modulemanager module
    providing service configuration for Laminas applications); these provide
    configuration for laminas-i18n services, including filters, validators, and
    view helpers.

### Deprecated

- [zendframework/zend-i18n#40](https://github.com/zendframework/zend-i18n/pull/40) deprecates
  `Laminas\I18n\View\HelperConfig`, as the functionality is made obsolete by the
  new `Module` class. The class will be removed with the 3.0 release.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#35](https://github.com/zendframework/zend-i18n/pull/35) updates the
  dependencies for laminas-validator and laminas-cache to use versions that are
  forwards-compatible with laminas-servicemanager v3, and re-enables their tests
  during continuous integration.

## 2.6.0 - 2016-02-10

### Added

- [zendframework/zend-i18n#8](https://github.com/zendframework/zend-i18n/pull/8) adds support for
  Vietnamese postal codes.
- [zendframework/zend-i18n#18](https://github.com/zendframework/zend-i18n/pull/18) adds support for
  `NumberFormatter` text attributes to the `NumberFormat` view helper.
- [zendframework/zend-i18n#28](https://github.com/zendframework/zend-i18n/pull/28),
  [zendframework/zend-i18n#29](https://github.com/zendframework/zend-i18n/pull/29),
  [zendframework/zend-i18n#30](https://github.com/zendframework/zend-i18n/pull/30),
  [zendframework/zend-i18n#31](https://github.com/zendframework/zend-i18n/pull/31), and
  [zendframework/zend-i18n#34](https://github.com/zendframework/zend-i18n/pull/34) prepared the
  documentation for publication at https://docs.laminas.dev/laminas-i18n/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#12](https://github.com/zendframework/zend-i18n/pull/12),
  [zendframework/zend-i18n#21](https://github.com/zendframework/zend-i18n/pull/21), and
  [zendframework/zend-i18n#22](https://github.com/zendframework/zend-i18n/pull/22) update the
  component to be forwards compatible with the v3 versions of laminas-stdlib,
  laminas-servicemanager, and laminas-eventmanager.
- [zendframework/zend-i18n#8](https://github.com/zendframework/zend-i18n/pull/8) updates the regex for
  the Mauritius postal code to follow the currently adopted format.
- [zendframework/zend-i18n#13](https://github.com/zendframework/zend-i18n/pull/13) updates the regex for
  Serbian postal codes to only accept 5 digits.
- [zendframework/zend-i18n#19](https://github.com/zendframework/zend-i18n/pull/19) fixes the behavior
  of the DateTime validator to ensure it can be called multiple times with
  multiple values.
- [zendframework/zend-i18n#33](https://github.com/zendframework/zend-i18n/pull/33) adds a check for
  null messages in `Translator::getTranslatedMessage()` to prevent illegal
  offset warnings.
