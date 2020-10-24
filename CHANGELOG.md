# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.11.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.11.0 - 2020-10-24

### Added

- [#40](https://github.com/laminas/laminas-i18n/pull/40) Add PHP 8.0 support


-----

### Release Notes for [2.11.0](https://github.com/laminas/laminas-i18n/milestone/2)

next feature release (minor)

### 2.11.0

- Total issues resolved: **0**
- Total pull requests resolved: **1**
- Total contributors: **1**

#### Enhancement,hacktoberfest-accepted

 - [40: Adds PHP 8.0 support and drops PHP support for versions &lt; 7.3](https://github.com/laminas/laminas-i18n/pull/40) thanks to @henrikthesing

## 2.10.3 - 2020-03-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed `replace` version constraint in composer.json so repository can be used as replacement of `zendframework/zend-i18n:^2.10.1`.

## 2.10.2 - 2020-03-20

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#29](https://github.com/laminas/laminas-i18n/pull/29) fixes typehint in `DateFormat` view helper.

## 2.10.1 - 2019-12-12

### Added

- [zendframework/zend-i18n#130](https://github.com/zendframework/zend-i18n/pull/130) adds support for PHP 7.4.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#129](https://github.com/zendframework/zend-i18n/pull/129) fixes Hungarian PhoneNumber validator (mobile numbers starting from 50).

## 2.10.0 - 2019-11-18

### Added

- [zendframework/zend-i18n#102](https://github.com/zendframework/zend-i18n/pull/102) adds `Laminas\I18n\View\HelperTrait`, which provides annotations describing the various helpers laminas-i18n provides to a laminas-view renderer. The trait can be used in combination with `Laminas\View\Renderer\PhpRenderer` in annotations on the `$this` variable within view scripts to provide IDE autocompletion for helper-provided methods.

### Changed

- [zendframework/zend-i18n#110](https://github.com/zendframework/zend-i18n/pull/110) modifies how `translatePlural()` works when a msgid is present, but no translations are present. It now properly returns the source-code if unable to translate the message, instead of returning an empty string (which is the behavior under `translate()` as well).

- [zendframework/zend-i18n#126](https://github.com/zendframework/zend-i18n/pull/126) modifies the package definition to put an explicit requirement on ext-intl, as it is required for the majority of functionality. Users have indicated multiple times confusion about why the component does not work after installation, when attempting to use intl functionality; requiring the extension resolves that issue.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.9.2 - 2019-09-30

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#122](https://github.com/zendframework/zend-i18n/pull/122) fixes PHP 7.4 compatibility.

## 2.9.1 - 2019-09-26

### Added

- [zendframework/zend-i18n#106](https://github.com/zendframework/zend-i18n/pull/106) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#114](https://github.com/zendframework/zend-i18n/pull/114) fixes PHP 7.4 compatibility.

- [zendframework/zend-i18n#116](https://github.com/zendframework/zend-i18n/pull/116) fixes DateTime formatter when changing timezone within the same instance.

- [zendframework/zend-i18n#100](https://github.com/zendframework/zend-i18n/pull/100) fixes regular expression in Liechtenstein postcode validator.

- [zendframework/zend-i18n#116](https://github.com/zendframework/zend-i18n/pull/116) fixes PhoneNumber validator to match the whole value.

- [zendframework/zend-i18n#103](https://github.com/zendframework/zend-i18n/pull/103) fixes Brazilian PhoneNumber validator.

- [zendframework/zend-i18n#117](https://github.com/zendframework/zend-i18n/pull/117) fixes French PhoneNumber validator.

- [zendframework/zend-i18n#119](https://github.com/zendframework/zend-i18n/pull/119) fixes Kosovan PhoneNumber validator.

- [zendframework/zend-i18n#120](https://github.com/zendframework/zend-i18n/pull/120) fixes Luxembourgish PhoneNumber validator.

## 2.9.0 - 2018-05-16

### Added

- [zendframework/zend-i18n#93](https://github.com/zendframework/zend-i18n/pull/93) adds two methods to `Translator`, `getCacheId(string $textDomain, string $locale)`,
  and `clearCache(string $textDomain, string $locale)`.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-i18n#96](https://github.com/zendframework/zend-i18n/pull/96) ensures that the `TranslatorServiceFactory` injects the configured `TranslatorPluginManager`,
  if any, before returning it. This ensures that configured remote loaders can be utilized.

## 2.8.0 - 2018-04-25

### Added

- [zendframework/zend-i18n#71](https://github.com/zendframework/zend-i18n/pull/71) adds a new option to the `IsInt` validator, `strict`. When set to boolean
  true, comparisons will use strict type validations. The default, `false`, retains the current
  behavior of non-strict comparisons. The class also exposes a `setStrict($flag)` method for
  enabling/disabling the behavior.

- [zendframework/zend-i18n#78](https://github.com/zendframework/zend-i18n/pull/78) and [zendframework/zend-i18n#91](https://github.com/zendframework/zend-i18n/pull/91) add support for PHP 7.2.

### Changed

- [zendframework/zend-i18n#81](https://github.com/zendframework/zend-i18n/pull/81) updates the list of allowed mobile prefixes for Chinese phones to match
  current changes to their systems.

- [zendframework/zend-i18n#80](https://github.com/zendframework/zend-i18n/pull/80) updates the PostCode validator to allow prefixing Latvian postal
  codes using the string `LV-`, which is now mandatory within Latvia. For BC purposes, the
  prefix is considered optional.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-i18n#78](https://github.com/zendframework/zend-i18n/pull/78) removes support for HHVM.

### Fixed

- [zendframework/zend-i18n#75](https://github.com/zendframework/zend-i18n/pull/75) fixes an issue with formatting currency values where
  the currency indicator is at the end of the value.

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
