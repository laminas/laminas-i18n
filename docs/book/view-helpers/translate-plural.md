# TranslatePlural

The `TranslatePlural` view helper can be used to **translate words which take
into account numeric meanings**. English, for example, has a singular definition
of "car", for one car, and the plural definition, "cars", meaning zero "cars"
or more than one car. Other languages like Russian or Polish have more plurals
with different rules.

The helper acts as a wrapper for the `Laminas\I18n\Translator\Translator` class.

## Setup

Before using the `TranslatePlural` view helper, you must have first created a
`Translator` object and have attached it to the view helper. If you use the
`Laminas\View\HelperPluginManager` to invoke the view helper, this will be done
automatically for you.

## Basic Usage

To use this view helper, you must define the following parameters:

- `$singular`: The message to use for singular values.
- `$plural`: The message to use for plural values.
- `$number`: The number to evaluate in order to determine which number to use.

```php
echo $this->translatePlural('car', 'cars', 1); // 'Auto'
echo $this->translatePlural('car', 'cars', 4); // 'Autos'
```

(The above example assumes that the environment locale is set to `de_DE`.)

## Using Text Domain

The text domain defines the domain of the translation.

```php fct_label="Invoke Usage"
echo $this->translatePlural('monitor', 'monitors', 1, 'customDomain'); // 'Monitor'
```

```php fct_label="Setter Usage"
$this->plugin('currencyFormat')->setTranslatorTextDomain('customDomain');

echo $this->translatePlural('monitor', 'monitors', 1); // 'Monitor'
```

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
echo $this->translatePlural('car', 'cars', 1, 'default', 'de_DE'); // 'Auto'
echo $this->translatePlural('car', 'cars', 4, 'default', 'de_DE'); // 'Autos'
```

### Default Value

By default, if no locale is provided, `TranslatePlural` will use the system
locale provide by PHP's `Locale::getDefault()`.
