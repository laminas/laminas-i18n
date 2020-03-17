# Plural

Most languages have specific rules for handling plurals. For instance, in
English, we say "0 cars" and "2 cars" (plural) while we say "1 car" (singular).
On the other hand, French uses the singular form for both 0 and 1 ("0 voiture"
and "1 voiture") and the plural form otherwise ("3 voitures").

Therefore we often need to handle plural cases even without translation
(mono-lingual application). The `Plural` helper was created for this.

> ### Plural Helper does not translate
>
> If you need to handle both plural cases *and* translations, you must use the
> [`TranslatePlural` helper](translate-plural.md); `Plural` does not translate.

Internally, the `Plural` helper uses the `Laminas\I18n\Translator\Plural\Rule` class to handle rules.

## Setup

Defining plural rules is left to the developer. To help you with this process,
here are some links with up-to-date plural rules for tons of languages:

- [www.unicode.org](https://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html)
- [developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals)

### Define plural Rules

As an example, you could add the following code in your
`Module` class:

```php
// Get the ViewHelperPlugin Manager from the ServiceManager, so we can fetch the
// Plural helper and add the plural rule for the application's language:
$viewHelperManager = $serviceManager->get('ViewHelperManager');
$pluralHelper      = $viewHelperManager->get('Plural');
```

#### Rule for French

```php
$pluralHelper->setPluralRule('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)');
```

The string reads as follows:

1. First, we specify how many plural forms we have. For French, only two (singular/plural).
2. Next, we specify the rule. Here, if the count is 0 or 1, this is rule n°0
   (singular) while it's rule n°1 otherwise.

#### Rule for English

As noted earlier earlier, English considers "1" as singular and "0/other" as
plural. Here is how that would be declared:

```php
$pluralHelper->setPluralRule('nplurals=2; plural=(n==1 ? 0 : 1)');
```

## Basic Usage

Now that we have defined the rule, we can use it in our view scripts.

### French

```php
echo $this->plural(['voiture', 'voitures'], 0); // 'voiture'
echo $this->plural(['voiture', 'voitures'], 1); // 'voiture'
echo $this->plural(['voiture', 'voitures'], 2); // 'voitures'
```

### English

```php
echo $this->plural(['car', 'cars'], 0); // 'cars'
echo $this->plural(['car', 'cars'], 1); // 'car'
```