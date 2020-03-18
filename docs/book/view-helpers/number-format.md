# NumberFormat

The `NumberFormat` view helper can be used to simplify **rendering of
locale-specific number and/or percentage strings**. It acts as a wrapper for the
[`NumberFormatter` class](https://www.php.net/NumberFormatter) within PHP's
internationalization extension (`ext/intl`).

## Basic Usage

```php
echo $this->numberFormat(1000); // '1,000'
```

By default, if no locale is provided, `NumberFormat` will use the system
locale provided by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `en_US`.)

## Using Format Style

This option sets the style of the formatting; one of the 
[`NumberFormatter` format style constants](https://www.php.net/manual/class.numberformatter.php#intl.numberformatter-constants.unumberformatstyle).

```php fct_label="Constructor Usage"
// Example 1
echo $this->numberFormat(0.8, NumberFormatter::PERCENT); // '80%'

// Example 2
echo $this->numberFormat(0.00123456789, NumberFormatter::SCIENTIFIC); // '1,23456789E-3'
```

```php fct_label="Setter Usage"
// Example 1
$this->plugin('numberFormat')->setFormatStyle(NumberFormatter::PERCENT);

echo $this->numberFormat(0.8); // '80%'

// Example 2
$this->plugin('numberFormat')->setFormatStyle(NumberFormatter::SCIENTIFIC);

echo $this->numberFormat(0.00123456789); // '1,23456789E-3'
```

(The above examples assumes that the environment locale is set to `en_US`.)

### Get current Value

To get the current value of this option, use the `getFormatStyle()` method.

```php
$this->plugin('numberFormat')->setFormatStyle(NumberFormatter::PERCENT);

echo $this->plugin('numberFormat')->getFormatStyle(); // 3 (NumberFormatter::DEFAULT_STYLE)
```

### Default Value

The default value of this option is `NumberFormatter::DEFAULT_STYLE`.

## Using Format Type

The format type speficied the [`NumberFormatter` formatting type](https://www.php.net/manual/class.numberformatter.php#intl.numberformatter-constants.types)
to use.

```php fct_label="Constructor Usage"
echo $this->numberFormat(1234567.89, null, NumberFormatter::TYPE_INT32); // '1.234.567'
```

```php fct_label="Setter Usage"
$this->plugin('numberFormat')->setFormatType(NumberFormatter::TYPE_INT32);

echo $this->numberFormat(1234567.89); // '1.234.567'
```

(The above examples assumes that the environment locale is set to `en_US`.)

### Get current Value

To get the current value of this option, use the `getFormatType()` method.

```php
$this->plugin('numberFormat')->setFormatType(NumberFormatter::DECIMAL);

echo $this->plugin('numberFormat')->getFormatType(); // 1 (NumberFormatter::DECIMAL)
```

### Default Value

The default value of this option is `NumberFormatter::TYPE_DEFAULT`.

## Using Locale

```php fct_label="Invoke Usage"
echo $this->numberFormat(1000, null, null, 'en_US'); // '1,000'
```

```php fct_label="Setter Usage"
$this->plugin('currencyFormat')->setLocale('en_US');

echo $this->numberFormat(1000); // '1,000'
```

```php fct_label="Locale Class Usage"
Locale::setDefault('en_US');

echo $this->numberFormat(1000); // '1,000'
```

### Get current Value

To get the current value of this option, use the `getLocale()` method.

```php
$this->plugin('numberFormat')->setLocale('en_US');

echo $this->plugin('numberFormat')->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `NumberFormat` will use the system
locale provided by PHP's `Locale::getDefault()`.

## Using Decimals

Sets the number of digits beyond the decimal point to display.

```php fct_label="Invoke Usage"
echo $this->numberFormat(1234, null, null, null, 5); // '1,234.00000'
```

```php fct_label="Setter Usage"
$this->plugin('currencyFormat')->setDecimals(5);

echo $this->numberFormat(1234); // '1,234.00000'
```

(The above examples assumes that the environment locale is set to `en_US`.)

### Get current Value

To get the current value of this option, use the `getDecimals()` method.

```php
$this->plugin('numberFormat')->setDecimals(5);

echo $this->plugin('numberFormat')->getDecimals(); // 5
```

### Default Value

The default value of this option is `null` that means the attributes for minimum
and maximum fraction digits will not be set on the `NumberFormatter`.

## Using Text Attributes

This option sets the text attributes of the formatting, like prefix and suffix 
for positive and negative numbers. See
[`NumberFormatter` text attribute constants](https://www.php.net/manual/class.numberformatter.php#intl.numberformatter-constants.unumberformattextattribute).

```php fct_label="Invoke Usage"
echo $this->numberFormat(
    -1000,
    null, // Format style
    null, // Format type
    null, // Locale
    null, // Decimals
    [
        NumberFormatter::NEGATIVE_PREFIX => '(minus) ',
    ]
); // '(minus) 1,000'
```

```php fct_label="Setter Usage"
$this->plugin('currencyFormat')->setTextAttributes([
    NumberFormatter::NEGATIVE_PREFIX => '(minus) ',
]);

echo $this->numberFormat(-1000); // '(minus) 1,000'
```

(The above examples assumes that the environment locale is set to `en_US`.)

### Get current Value

To get the current value of this option, use the `getTextAttributes()` method.

```php
$this->plugin('numberFormat')->setTextAttributes([
    NumberFormatter::POSITIVE_PREFIX => '(plus) ',
    NumberFormatter::NEGATIVE_PREFIX => '(minus) ',
]);

var_dump($this->plugin('numberFormat')->getTextAttributes()); // ['(plus) ', '(minus) ']
```

### Default Value

The default value of this option is an empty `array`;
