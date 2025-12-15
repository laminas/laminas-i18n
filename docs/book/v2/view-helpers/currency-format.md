# CurrencyFormat

The `CurrencyFormat` view helper can be used to simplify **rendering of
localized currency values**. It acts as a wrapper for the
[`NumberFormatter` class](https://www.php.net/NumberFormatter) within the PHP's
internationalization extension (`ext/intl`).

## Basic Usage

```php
echo $this->currencyFormat(1234.56, 'USD'); // '$1,234.56'
```

By default, if no locale is provided, `CurrencyFormat` will use the system
locale provided by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `en_US`.)

## Using Currency Code

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Invoke Usage"
    ```php
    echo $this->currencyFormat(1234.56, 'EUR'); // '1.234,56 €'
    ```

=== "Setter Usage"
    ```php
    $this->plugin('currencyFormat')->setCurrencyCode('EUR');

    echo $this->currencyFormat(1234.56); // '1.234,56 €'
    ```
<!-- markdownlint-restore -->

(The above example assumes that the environment locale is set to `de`.)

### Get current Value

To get the current value of this option, use the `getCurrencyCode()` method.

```php
$this->plugin('currencyFormat')->setCurrencyCode('USD');

echo $this->plugin('currencyFormat')->getCurrencyCode(); // 'USD'
```

### Default Value

The default value of this option is `null`.

## Show or hide Decimals

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Invoke Usage"
    ```php
    echo $this->currencyFormat(1234.56, 'EUR', false); // '1.234 €'
    ```

=== "Setter Usage"
    ```php
    $this->plugin('currencyFormat')->setShouldShowDecimals(false);

    echo $this->currencyFormat(1234.56); // '1.234 €'
    ```
<!-- markdownlint-restore -->

(The above example assumes that the environment locale is set to `de`.)

### Get current Value

To get the current value of this option, use the `shouldShowDecimals()` method.

```php
$this->plugin('currencyFormat')->setShouldShowDecimals(true);

echo $this->plugin('currencyFormat')->shouldShowDecimals(); // true
```

### Default Value

The default value of this option is `null` that means the decimals are showing.

## Using Locale

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Invoke Usage"
    ```php
    echo $this->currencyFormat(1234.56, 'EUR', null, 'de'); // '1.234,56 €'
    ```

=== "Setter Usage"
    ```php
    $this->plugin('currencyFormat')->setLocale('de');

    echo $this->currencyFormat(1234.56, 'EUR'); // '1.234,56 €'
    ```
<!-- markdownlint-restore -->

### Get current Value

To get the current value of this option, use the `getLocale()` method.

```php
$this->plugin('currencyFormat')->setLocale('de');

echo $this->plugin('currencyFormat')->getLocale(); // 'de'
```

### Default Value

By default, if no locale is provided, `CurrencyFormat` will use the system
locale provided by PHP's `Locale::getDefault()`.

## Define custom Pattern

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Invoke Usage"
    ```php
    echo $this->currencyFormat(1234.56, 'EUR', null, 'de', '#0.# kg'); // '12345678,90 kg'
    ```

=== "Setter Usage"
    ```php
    $this->plugin('currencyformat')->setCurrencyPattern('#0.# kg');

    echo $this->currencyFormat(1234.56, 'EUR'); // '12345678,90 kg'
    ```
<!-- markdownlint-restore -->

(The above example assumes that the environment locale is set to `de`.)

Valid patterns are documented at
[ICU DecimalFormat](https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classDecimalFormat.html#details);
see the [NumberFormatter::setPattern documentation](https://www.php.net/manual/numberformatter.setpattern.php)
for more information.

### Get current Value

To get the current value of this option, use the `getCurrencyPattern()` method.

```php
$this->plugin('currencyFormat')->setCurrencyPattern('#0.# kg');

echo $this->plugin('currencyFormat')->getCurrencyPattern(); // '#0.# kg'
```

### Default Value

The default value of this option is `null`.

## Multiple Executions

If the different options are set prior to formatting then it will be applied
each time the helper is used.

```php
$this->plugin('currencyformat')->setCurrencyCode('USD')->setLocale('en_US');

echo $this->currencyFormat(1234.56); // '$1,234.56'
echo $this->currencyFormat(5678.90); // '$5,678.90'
```
