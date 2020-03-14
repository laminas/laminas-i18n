# PostCode

`Laminas\I18n\Validator\PostCode` allows you to determine if a given value **is a
valid postal code**. Postal codes are specific to cities, and in some locales
termed ZIP codes.

`Laminas\I18n\Validator\PostCode` knows more than 160 different postal code
formats. To select the correct format there are two ways. You can either use a
fully qualified locale, or you can set your own format manually.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\PostCode();

var_dump($validator->isValid(1010)); // true
```

By default, if no country code is provided, `PostCode` will use the system
locale provide by PHP's `Locale::getDefault()` and `Locale::getRegion()` to
extract the region code.

(The above example assumes that the environment locale is set to `de_AT`.)

## Using Locale

Using a locale is more convenient as laminas-validator already knows the
appropriate postal code format for each locale; however, you need to use the
fully qualified locale (one containing a region specifier) to do so. For
instance, the locale `de` is a locale but could not be used with
`Laminas\I18n\Validator\PostCode` as it does not include the region; `de_AT`,
however, would be a valid locale, as it specifies the region code (`AT`, for
Austria).

```php fct_label="Constructor Usage"
$validator = new Laminas\I18n\Validator\PostCode(['locale' => 'de_AT']);

$validator->isValid(1010); // true
```

```php fct_label="Setter Usage"
$validator = new Laminas\I18n\Validator\PostCode();
$validator->setLocale('de_AT');

$validator->isValid(1010); // true
```

```php fct_label="Locale Class Usage"
Locale::setDefault('de_AT');

$validator = new Laminas\I18n\Validator\PostCode();

$validator->isValid(1010); // true
```

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$validator = new Laminas\I18n\Validator\PostCode(['locale' => 'de_AT']);

echo $validator->getLocale(); // 'de_AT'
```

### Default Value

By default, if no locale is provided, `PostCode` will use the system locale
provide by PHP's `Locale::getDefault()` and `Locale::getRegion()` to extract
the region code.

## Using Custom Format

Postal code formats are regular expression strings. When the international
postal code format, which is used by setting the locale, does not fit your
needs, then you can also manually set a format by calling `setFormat()`.


```php fct_label="Constructor Usage"
$validator = new Laminas\I18n\Validator\PostCode(['format' => 'AT-\d{4}']);

$validator->isValid('AT-1010'); // true
```

```php fct_label="Setter Usage"
$validator = new Laminas\I18n\Validator\PostCode();
$validator->setFormat('AT-\d{4}');

$validator->isValid('AT-1010'); // true
```

### Conventions for self defined Formats

When using self defined formats, you should omit the regex delimiters and
anchors (`'/^'` and  `'$/'`). They are attached automatically.

You should also be aware that postcode values will always be validated in a
strict way. This means that they have to be written standalone without
additional characters when they are not covered by the format.

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$validator = new Laminas\I18n\Validator\PostCode(['format' => 'AT-\d{4}']);

echo $validator->getFormat(); // 'AT-\d{4}'
```

### Default Value

The default value of this option is `null`.

## Using Callback Service

The `PostCode` validator allows additional validations via an optional service
callback. The callback runs before the standard validation of the `PostCode`
class.

Internally, the additional validation is done by
[laminas-validator's `Callback`](https://docs.laminas.dev/laminas-validator/validators/callback/)
class.

```php fct_label="Constructor Usage"
$validator = new Laminas\I18n\Validator\PostCode(
    [
        'service' => function ($value) {
            // Allow only certain districts in town
            if (in_array($value, range(1010, 1423), false)) {
                return true;
            }

            return false;
        },
        'locale'  => 'de_AT',
    ]
);

var_dump($validator->isValid(1010)); // true
var_dump($validator->isValid(1600)); // false
```

```php fct_label="Setter Usage"
$validator = new Laminas\I18n\Validator\PostCode();
$validator->setService(function ($value) {
   // Allow only certain districts in town
   if (in_array($value, range(1010, 1423), false)) {
       return true;
   }

   return false;
});
$validator->setLocale('de_AT');

var_dump($validator->isValid(1010)); // true
var_dump($validator->isValid(1600)); // false
```

### Default Value

The default value of this option is `null`.
