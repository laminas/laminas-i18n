# Migration to Version 2.4

## General

Version 2.4 adds support for PHP 7.

## Validators

### IsFloat

In PHP 7, `float` is a reserved keyword, which required renaming the
`Laminas\I18n\Validator\Float` validator. If you were using the
`Laminas\I18n\Validator\Float` validator directly previously, you will now
receive an `E_USER_DEPRECATED` notice on instantiation. Please update your code
to refer to the `Laminas\I18n\Validator\IsFloat` class instead.

Users pulling their `Laminas\I18n\Validator\Float` validator instance from the
validator plugin manager receive an `Laminas\I18n\Validator\IsFloat` instance
instead starting in 2.4.0.

### IsInt

In PHP 7, `int` is a reserved keyword, which required renaming the 
`Laminas\I18n\Validator\Int` validator. If you were using the
`Laminas\I18n\Validator\Int` validator directly previously, you will now
receive an `E_USER_DEPRECATED` notice on instantiation. Please update your code
to refer to the `Laminas\I18n\Validator\IsInt` class instead.

Users pulling their `Laminas\I18n\Validator\Int` validator instance from the
validator plugin manager receive an `Laminas\I18n\Validator\IsInt` instance
instead starting in 2.4.0.
