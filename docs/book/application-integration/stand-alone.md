# Stand-Alone

All filters, validators, view-helpers and the translator of laminas-i18n can
also be used stand-alone.

## Translator

### Setup

Create a file for the translation messages. For example `languages/de_DE.php`:

```php
return [
    'car'   => 'Auto',
    'train' => 'Zug',
];
```

Create the translator instance and add the translation file.

```php
$translator = new Laminas\I18n\Translator\Translator();
$translator->addTranslationFile(
    Laminas\I18n\Translator\Loader\PhpArray::class,
    __DIR__ . '/languages/de_DE.php',
    'default',
    'de_DE'
);
```

### Translate Messages

```php
$translator->setLocale('de_DE');

echo $translator->translate('car');   // Auto
echo $translator->translate('train'); // Zug
```

## Filters

Each filter can be used directly.

```php
$filter = new Laminas\I18n\Filter\Alnum();

echo $filter->filter('This is (my) content: 123'); // "Thisismycontent123"
```

### Using Filter Plugin Manager

Register all standard filters of laminas-i18n in the filter-plugin-manager:

```php
$filterManager = new Laminas\Filter\FilterPluginManager(
    new Laminas\ServiceManager\ServiceManager()
);
$filterManager->configure(
    (new Laminas\I18n\ConfigProvider())->getFilterConfig()
);
```

Get a filter:

```php
/** @var Laminas\I18n\Filter\Alnum $filter */
$filter = $filterManager->get(Laminas\I18n\Filter\Alnum::class);
```

## Validators

Each validator can be used directly.

```php
$validator = new Laminas\I18n\Validator\Alnum();

$result = $validator->isValid('Abcd12')); // true
```

### Using Validator Plugin Manager

Register all standard validators of laminas-i18n in the validator-plugin-manager:

```php
$validatorManager = new Laminas\Validator\ValidatorPluginManager(
    new Laminas\ServiceManager\ServiceManager()
);
$validatorManager->configure(
    (new Laminas\I18n\ConfigProvider())->getValidatorConfig()
);
```

Get a validator:

```php
/** @var Laminas\I18n\Validator\Alnum $validator */
$validator = $validatorManager->get(Laminas\I18n\Validator\Alnum::class);
```

## View Helpers

### Setup laminas-view

Create the renderer:

```php
$renderer = new Laminas\View\Renderer\PhpRenderer();
```

Register all standard view-helpers of laminas-i18n in the helper-plugin-manager:

```php
$renderer->getHelperPluginManager()->configure(
    (new Laminas\I18n\ConfigProvider())->getViewHelperConfig()
);
```

### Using Helper

```php
echo $renderer->currencyFormat(1234.56, 'USD', null, 'en_US'); // "$1,234.56"
```
