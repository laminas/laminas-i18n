# Format Examples

## PHP

For example `languages/de_DE.php`:

```php
return [
    // Message => Translation
    'car'   => 'Auto',
    'train' => 'Zug',
];
```

### Plural

For example `languages/en_GB.php`:

```php
return [
    // Rule for English
    '' => [
        'plural_forms' => 'nplurals=2; plural=(n==1 ? 0 : 1)'
    ],
    // Messages
    'car'   => 'car',
    'train' => 'train',
];
```

For example `languages/fr_FR.php`:

```php
return [
    // Rule for French
    '' => [
        'plural_forms' => 'nplurals=2; plural=(n==0 || n==1 ? 0 : 1)'
    ],
    // Messages
    'car'   => 'voiture',
    'train' => 'train',
];
```

## INI

### Normal Syntax

For example `languages/de_DE.ini`:

```ini
; Message
identifier1.message = "car"
; Translation
identifier1.translation = "Auto"

identifier2.message = "train"
identifier2.translation = "Zug"
```

### Simple Syntax

For example `languages/de_DE.ini`:

```ini
; Message
identifier1[] = "car"
; Translation
identifier1[] = "Auto"

identifier2[] = "train"
identifier2[] = "Zug"
```

### Plural

For example `languages/en_GB.ini`:

```ini
[plural]
plural_forms = 'nplurals=2; plural=(n==1 ? 0 : 1)'

[translation]
identifier1.message = "car"
identifier1.translation = "car"

identifier2.message = "train"
identifier2.translation = "train"
```

For example `languages/fr_FR.ini`:

```ini
[plural]
plural_forms = 'nplurals=2; plural=(n==0 || n==1 ? 0 : 1)'

[translation]
identifier1.message = "car"
identifier1.translation = "voiture"

identifier2.message = "train"
identifier2.translation = "train"
```