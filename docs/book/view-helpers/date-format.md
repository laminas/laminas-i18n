# DateFormat

The `DateFormat` view helper can be used to simplify **rendering of localized
date/time values**. It acts as a wrapper for the `IntlDateFormatter` class
within PHP's internationalization extension (`ext/intl`).

## Basic Usage

The value for the date must be a `DateTime` instance, an integer representing a
Unix timestamp value, or an array in the format returned by `localtime()`.

Example with a `DateTime` instance:

```php
echo $this->dateFormat(new DateTime()); // '20190222 09:07 PM'
```

Example with an Unix timestamp:

```php
echo $this->dateFormat(1550870660); // '20190222 04:24 PM'
```

Example with the format of `localtime()`:

```php
echo $this->dateFormat([
    'tm_sec'  => 0,   // seconds, 0 to 59
    'tm_min'  => 30,  // minutes, 0 to 59
    'tm_hour' => 12,  // hours, 0 to 23
    'tm_mday' => 5,   // day of the month, 1 to 31
    'tm_mon'  => 4,   // month of the year, 0 (Jan) to 11 (Dec)
    'tm_year' => 119, // years since 1900
]); // '20190505 12:30 PM'
```

By default, if no locale is provided, `DateFormat` will use the system
locale provide by PHP's `Locale` class and the `getDefault()` method.

(The above example assumes that the environment locale is set to `en_US`.)

### More Examples

Format date and time:

```php
echo $this->dateFormat(
    new DateTime(),
    IntlDateFormatter::MEDIUM, // Date
    IntlDateFormatter::MEDIUM  // Time
); // 'Feb 22, 2019, 9:07:38 PM'
```

Format only a date:

```php
echo $this->dateFormat(
    new DateTime(),
    IntlDateFormatter::LONG, // Date
    IntlDateFormatter::NONE  // Time
); // 'Feb 22, 2019'
```

Format only a time:

```php
echo $this->dateFormat(
    new DateTime(),
    IntlDateFormatter::NONE, // Date
    IntlDateFormatter::SHORT // Time
); // '9:07 PM'
```

(The above examples assumes that the environment locale is set to `en_US`.)

## Using Date Type

Sets date type to use (none, short, medium, long, full).

```php
echo $this->dateFormat(new DateTime(), IntlDateFormatter::MEDIUM); // 'Feb 22, 2019'
```

(The above example assumes that the environment locale is set to `en_US`.)

Possible values for the date type option are the following
[constants of PHP's `IntlDateFormatter` class](http://www.php.net/manual/class.intldateformatter.php#intl.intldateformatter-constants):

* `IntlDateFormatter::NONE` - Do not include this element
* `IntlDateFormatter::FULL` - Fullstyle (Tuesday, April 12, 1952 AD)
* `IntlDateFormatter::LONG` - Long style (January 12, 1952)
* `IntlDateFormatter::MEDIUM` - Medium style (Jan 12, 1952)
* `IntlDateFormatter::SHORT` - Short style (12/13/52)

### Default Value

The default value of this option is `IntlDateFormatter::NONE`.

## Using Time Type

Sets time type to use (none, short, medium, long, full).

```php
echo $this->dateFormat(new DateTime(), IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM);
// '9:41:58 PM'
```

(The above example assumes that the environment locale is set to `en_US`.)

Possible values for the date type option are the following
[constants of PHP's `IntlDateFormatter` class](http://www.php.net/manual/class.intldateformatter.php#intl.intldateformatter-constants):

* `IntlDateFormatter::NONE` - Do not include this element
* `IntlDateFormatter::FULL` - Fullstyle (3:30:42pm PST)
* `IntlDateFormatter::LONG` - Long style (3:30:32pm)
* `IntlDateFormatter::MEDIUM` - Medium style (3:30:32pm)
* `IntlDateFormatter::SHORT` - Short style (3:30pm)

### Default Value

The default value of this option is `IntlDateFormatter::NONE`.

## Using Locale

```php fct_label="Invoke Usage"
echo $this->dateFormat(new DateTime(), null, null, 'de_DE');
// 'Freitag, 22. Februar 2019 um 21:16:37 GMT'
```

```php fct_label="Setter Usage"
$this->plugin('dateFormat')->setLocale('de_DE');

echo $this->dateFormat(new DateTime()); // 'Freitag, 22. Februar 2019 um 21:16:37 GMT'
```

```php fct_label="Locale Class Usage"
Locale::setDefault('de_DE');

echo $this->dateFormat(new DateTime()); // 'Freitag, 22. Februar 2019 um 21:16:37 GMT'
```

### Get current Value

To get the current value of this option, use the `getLocale()` method.

```php
$this->plugin('dateFormat')->setLocale('en_US');

echo $this->plugin('dateFormat')->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `DateFormat` will use the system
locale provide by PHP's `Locale::getDefault()`.

## Using Timezone

By default, the system's default timezone will be used when formatting. This
overrides any timezone that may be set inside a `DateTime` object. To change the
timezone when formatting, use the `setTimezone()` method.

```php
$this->plugin('dateFormat')->setTimezone('America/New_York');

echo $this->dateFormat(new DateTime(), null, null, 'en_US');
// 'Friday, February 22, 2019 at 4:20:21 PM Eastern Standard Time'
```

### Get current Value

To get the current value of this option, use the `getTimezone()` method.

```php
$this->plugin('dateFormat')->setTimezone('America/New_York');

echo $this->plugin('dateFormat')->getTimezone(); // 'America/New_York'
```

### Default Value

By default, if no timezone is provided, `DateFormat` will use the system
timezone provide by PHP's `date_default_timezone_get()`.
