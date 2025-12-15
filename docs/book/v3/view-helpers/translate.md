# Translate

The `Translate` view helper can be used to **translate content**. It acts as a
wrapper for the [`Laminas\I18n\Translator\Translator` class](../translation.md).

## Setup

Before using the `Translate` view helper, you must have first created a
`Translator` object and have attached it to the view helper. If you use the
`Laminas\View\HelperPluginManager` to invoke the view helper, this will be done
automatically for you.

## Basic Usage

```php
echo $this->translate('Some translated text.'); // Etwas übersetzter Text
```

(The above example assumes that the environment locale is set to `de_DE`.)

## Using Text Domain

The text domain defines the domain of the translation.

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Invoke Usage"
    ```php
    echo $this->translate('monitor', 'customDomain'); // 'Monitor'
    ```

=== "Setter Usage"
    ```php
    $this->plugin('currencyFormat')->setTranslatorTextDomain('customDomain');

    echo $this->translate('monitor'); // 'Monitor'
    ```
<!-- markdownlint-restore -->

(The above example assumes that the environment locale is set to `de_DE`.)

### Get current Value

To get the current value of this option, use the `getTranslatorTextDomain()`
method.

```php
$this->plugin('translatePlural')->setTranslatorTextDomain('customDomain');

echo $this->plugin('translatePlural')->getTranslatorTextDomain(); // 'customDomain'
```

### Default Value

The default value of this option is `default` like defined in
`Laminas\I18n\Translator\TranslatorInterface`.

## Using Locale

The locale to which the message should be translated.

```php
echo $this->translate('car', 'default', 'de_DE'); // 'Auto'
```

### Default Value

By default, if no locale is provided, `TranslatePlural` will use the system
locale provided by PHP's `Locale::getDefault()`.
