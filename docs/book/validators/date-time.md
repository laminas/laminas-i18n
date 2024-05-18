# DateTime

`Laminas\I18n\Validator\DateTime` allows you to determine if a given value **is a
valid date, time or datetime**.
Internally, PHP's `IntlDateFormatter` tries to create a date time for the given
value and locale.

## Basic Usage

```php
$validator = new Laminas\I18n\Validator\DateTime();

var_dump($validator->isValid('20190228 10:00 pm')); // true
var_dump($validator->isValid('20190229 10:00 pm')); // false
var_dump($validator->isValid('20200229 10:00 pm')); // true
```

By default, if no locale is provided, `DateTime` will use the system locale
provided by PHP's `Locale::getDefault()` and the default timezone provided
by PHP's `date_default_timezone_get`.

(The above example assumes that the environment locale is set to `en_EN` and
the timezone is set to `Europe/London`.)

## Set Locale

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime(['locale' => 'de']);

    var_dump($validator->isValid('29.02.2020')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setLocale('de');

    var_dump($validator->isValid('29.02.2020')); // true
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `getLocale()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['locale' => 'en_US']);

echo $validator->getLocale(); // 'en_US'
```

### Default Value

By default, if no locale is provided, `DateTime` will use the system locale
provided by PHP's `Locale::getDefault()`.

## Define Custom Pattern

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime(['pattern' => 'yyyy-MM-DD']);

    var_dump($validator->isValid('2019-02-28')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setPattern('yyyy-MM-DD');

    var_dump($validator->isValid('2019-02-28')); // true
    ```
<!-- markdownlint-restore -->

Possible patterns are documented at
[http://userguide.icu-project.org/formatparse/datetime](http://userguide.icu-project.org/formatparse/datetime).

### Get Current Value

To get the current value of this option, use the `getPattern()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['pattern' => 'yyyy-MM-DD']);

echo $validator->getPattern(); // 'yyyy-MM-DD'
```

### Default Value

The default value of this option is `null`.

## Using Date Type

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime(['date_type' => IntlDateFormatter::MEDIUM]);

    var_dump($validator->isValid('Feb 28, 2020')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setDateType(IntlDateFormatter::MEDIUM);

    var_dump($validator->isValid('Feb 28, 2020')); // true
    ```
<!-- markdownlint-restore -->

Possible values for the date type option are the following
[constants of PHP's `IntlDateFormatter` class](https://www.php.net/manual/class.intldateformatter.php#intl.intldateformatter-constants):

- `IntlDateFormatter::NONE` - Do not include this element
- `IntlDateFormatter::FULL` - Fullstyle (Tuesday, April 12, 1952 AD)
- `IntlDateFormatter::LONG` - Long style (January 12, 1952)
- `IntlDateFormatter::MEDIUM` - Medium style (Jan 12, 1952)
- `IntlDateFormatter::SHORT` - Short style (12/13/52)

### Get Current Value

To get the current value of this option, use the `getDateType()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['date_type' => IntlDateFormatter::MEDIUM]);

echo $validator->getDateType(); // 'MMM d, y' (IntlDateFormatter::MEDIUM)
```

### Default Value

The default value of this option is `IntlDateFormatter::NONE`.

## Using Time Type

Sets time type to use (none, short, medium, long, full).

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime(['time_type' => IntlDateFormatter::MEDIUM]);

    var_dump($validator->isValid('8:05:40 pm')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setTimeType(IntlDateFormatter::MEDIUM);

    var_dump($validator->isValid('8:05:40 pm')); // true
    ```
<!-- markdownlint-restore -->

Possible values for the date type option are the following
[constants of PHP's `IntlDateFormatter` class](https://www.php.net/manual/class.intldateformatter.php#intl.intldateformatter-constants):

- `IntlDateFormatter::NONE` - Do not include this element
- `IntlDateFormatter::FULL` - Fullstyle (3:30:42pm PST)
- `IntlDateFormatter::LONG` - Long style (3:30:32pm)
- `IntlDateFormatter::MEDIUM` - Medium style (3:30:32pm)
- `IntlDateFormatter::SHORT` - Short style (3:30pm)

### Get Current Value

To get the current value of this option, use the `getTimeType()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['time_type' => IntlDateFormatter::MEDIUM]);

echo $validator->getTimeType(); // 'h:mm:ss a' (IntlDateFormatter::MEDIUM)
```

### Default Value

The default value of this option is `IntlDateFormatter::NONE`.

## Using Calendar

To demonstrate the calendar option, additional settings are needed.

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime([
        'calendar'  => IntlDateFormatter::TRADITIONAL,
        'date_type' => IntlDateFormatter::MEDIUM,
        'locale'    => 'de_DE@calendar=buddhist',
        'timezone'  => 'Europe/Berlin',
    ]);

    var_dump($validator->isValid('28.02.2562 BE')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setCalendar(IntlDateFormatter::TRADITIONAL);
    $validator->setDateType(IntlDateFormatter::MEDIUM);
    $validator->setLocale('de_DE@calendar=buddhist');
    $validator->setTimezone('Europe/Berlin');

    var_dump($validator->isValid('28.02.2562 BE')); // true
    ```
<!-- markdownlint-restore -->

Possible values for the calendar option are the following
[constants of PHP's `IntlDateFormatter` class](https://www.php.net/manual/class.intldateformatter.php#intl.intldateformatter-constants):

- `IntlDateFormatter::TRADITIONAL` - Non-Gregorian Calendar
- `IntlDateFormatter::GREGORIAN` - Gregorian Calendar

### Get Current Value

To get the current value of this option, use the `getCalendar()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['calendar' => IntlDateFormatter::TRADITIONAL]);

echo $validator->getCalendar(); // '0' (IntlDateFormatter::TRADITIONAL)
```

### Default Value

The default value of this option is `IntlDateFormatter::GREGORIAN`.

## Using Timezone

<!-- markdownlint-disable MD038 MD009 MD046 -->
=== "Constructor Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime(['timezone' => 'Europe/London']);

    var_dump($validator->isValid('20190228 10:00 pm')); // true
    ```

=== "Setter Usage"
    ```php
    $validator = new Laminas\I18n\Validator\DateTime();
    $validator->setTimezone('Europe/London');

    var_dump($validator->isValid('20190228 10:00 pm')); // true
    ```
<!-- markdownlint-restore -->

### Get Current Value

To get the current value of this option, use the `getTimezone()` method.

```php
$validator = new Laminas\I18n\Validator\DateTime(['timezone' => 'Europe/London']);

echo $validator->getTimezone(); // 'Europe/London'
```

### Default Value

By default, if no timezone is provided, `DateTime` will use the system timezone
provided by PHP's `date_default_timezone_get()`.
