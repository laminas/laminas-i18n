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

Configure the dependency-injection container with the help of the [config provider](https://docs.laminas.dev/laminas-config-aggregator/config-providers/) and add the translation file via configuration:

```php
$container = (new Laminas\ServiceManager\ServiceManager(
    (new Laminas\I18n\ConfigProvider())->getDependencyConfig()
));
$container->setService(
    'config',
    [
        'laminas-i18n' => [
            'translator' => [
                'translation_files' => [
                    [
                        'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
                        'filename' => __DIR__ . '/languages/de_DE.php',
                        'locale'   => 'de_DE',
                    ],
                ],
            ],
        ],
    ]
);
```

Fetch the translator from the container:

```php
$translator = $container->get(Laminas\Translator\TranslatorInterface::class);
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

> MISSING: **Installation Requirements**
> Starting with version 3.0, the core `laminas-i18n` package no longer ships with these view helpers out of the box.
> They have been extracted into an optional satellite package.
>
> To use internationalization view helpers in an application, the satellite package must be installed, which will automatically bring in `laminas-view` as a required dependency:
>
> ```bash
> $ composer require laminas/laminas-i18n-view
> ```

### Setup laminas-view

Configure the dependency-injection container with the help via the config providers of laminas-view, laminas-i18n, and laminas-i18n-view:

```php
$container = new Laminas\ServiceManager\ServiceManager();
$container->configure((new Laminas\View\ConfigProvider())->getDependencies());
$container->configure((new Laminas\I18n\ConfigProvider())->getDependencyConfig());
$container->configure((new Laminas\I18n\View\ConfigProvider())()['dependencies']);
```

Register all standard view-helpers of laminas-i18n-view in the helper-plugin-manager:

```php
$viewHelperManager = $container->get(Laminas\View\HelperPluginManager::class);
$viewHelperManager->configure(
    (new Laminas\I18n\View\ConfigProvider())()['view_helpers']
);
```

### Using Helper

```php
$helper = $viewHelperManager->get(Laminas\I18n\View\Helper\CurrencyFormat::class);

$helper->amount(123.45, 'EUR'); // "€ 123.45"
```
