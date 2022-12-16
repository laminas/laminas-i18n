# CountryCodeDataList

The `CountryCodeDataList` view helper can be used to render an HTML [&lt;datalist&gt;](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist) element containing a list of ISO-3166 country codes with two-letter country codes and localised country names.

## Basic Usage

```php
echo $this->countryCodeDataList();
```

By default, the helper will output a `datalist` with labels in the systems default locale such as:

```html
<datalist >
    <option value="AD" label="Andorra">
    <option value="AE" label="United&#x20;Arab&#x20;Emirates">
    <option value="AF" label="Afghanistan">
    <option value="AG" label="Antigua&#x20;&amp;&#x20;Barbuda">
    <!-- …more items… -->
</datalist>
```

## Customising Output

### Override the Locale at Runtime

The first, optional argument to the helper is a locale string used to format the country names:

```php
echo $this->countryCodeDataList('de_DE'); // or just 'DE'
```

Outputs:

```html
<datalist >
    <option value="AD" label="Andorra">
    <option value="AE" label="Vereinigte&#x20;Arabische&#x20;Emirate">
    <option value="AF" label="Afghanistan">
    <option value="AG" label="Antigua&#x20;und&#x20;Barbuda">
    <!-- etc… -->
</datalist>
```

### Set HTML Attributes on the Data List

You can provide [HTML attributes](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist#attributes) to the second argument of the helper as an associative array.

```php
echo $this->countryCodeDataList(null, [
    'id' => 'country-codes',
]); // or just 'DE'
```

Outputs:

```html
<datalist id="country-codes">
    <option value="AD" label="Andorra">
    <!-- etc… -->
</datalist>
```

## Restricting the List of Available Countries

### The `CountryCodeListInterface` Contract

The helper's constructor accepts an object that implements `Laminas\I18n\Geography\CountryCodeListInterface`.
This interface has no specific methods other than those it inherits from [`IteratorAggregate`](https://www.php.net/manual/en/class.iteratoraggregate.php) and [`Countable`](https://www.php.net/manual/en/class.countable.php).
This object must be an iterable that yields a list of `Laminas\I18n\CountryCode` value objects.

The default implementation of this interface, `Laminas\I18n\Geography\DefaultCountryCodeList` provides a list of all known ISO-3166 codes.

### Overriding the List

Once you have an implementation of `CountryCodeListInterface` that provides the list of countries that you require, the list must be made available to the view helper.

This can be achieved in 2 ways:

#### 1. A Custom Factory for the Helper

In order to inject the list into the helper, you can set up a factory that replaces the view helpers default factory.

Create a file for the factory e.g. `module/MyModule/src/Factory/MyCountryDataListFactory.php` with the following contents:

```php
namespace MyNameSpace;

use Laminas\Escaper\Escaper;
use Laminas\I18n\View\Helper\CountryCodeDataList;
use Psr\Container\ContainerInterface;

class MyCountryDataListFactory
{
    public function __invoke(ContainerInterface $container): CountryCodeDataList
    {
        return new CountryCodeDataList(
            $container->get('ContainerIdForMyCountryCodeList'),
            new Escaper(),
            'en_US',
        );
    }
}
```

You would then configure your application config to register this factory for the helper.

For example, in a Laminas MVC module, the file might be `module/MyModule/config/module.config.php`

```php
return [
    'view_helpers' => [
        'factories' => [
            \Laminas\I18n\View\Helper\CountryCodeDataList::class => \MyNameSpace\MyCountryDataListFactory::class,
        ],
    ],
];
```

#### 2. Override the List Itself in the DI Container

Assuming your customised list is available in the DI container with the alias `MyCountryList`, modify your application configuration so that the list interface is aliased to your own implementation:

Example configuration file path for a Laminas MVC module: `module/MyModule/config/module.config.php`

```php
return [
    /**
     * 'service_manager' is the key used in a Laminas MVC application.
     * For a Mezzio application, the key would be 'dependencies' 
     */
    'service_manager' => [
        'aliases' => [
            \Laminas\I18n\Geography\CountryCodeListInterface::class => 'MyCountryList',
        ],
    ],
];
```

In the above scenario, the shipped factory for the view helper will inject your implementation of the country list into the view helper's constructor.
