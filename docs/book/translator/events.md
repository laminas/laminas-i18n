# Events

The translator of laminas-i18n triggers two events during the processing of
translations:

* `Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION`
* `Laminas\I18n\Translator\Translator::EVENT_NO_MESSAGES_LOADED`

The typical usage for these events is to log missing translations and track when
the loading of messages fails.

> ### Installation requirements
>
> The event support of laminas-i18n depends on the
> [laminas-eventmanager](https://docs.laminas.dev/laminas-eventmanager/)
> component, so be sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-eventmanager
> ```

## Basic Usage

```php
// Set locale
Locale::setDefault('de_DE');

// Create translator
$translator = new Laminas\I18n\Translator\Translator();

// Enable event manager
$translator->enableEventManager();

// Attach listener
$translator->getEventManager()->attach(
    Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION,
    static function (Laminas\EventManager\EventInterface $event) {
        var_dump($event->getName());
        // 'missingTranslation' (Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION)
        var_dump($event->getParams());
        // ['message' => 'car', 'locale' => 'de_DE', 'text_domain' => 'default']
    }
);

// Trigger related events
echo $translator->translate('car');
```

## Using Event Manager

### Enable Event Manager

To enable the event manager, call the `enableEventManager()` method.

```php
$translator->enableEventManager();
```

The event manager can also be [enabled per factory](factory.md#enable-eventmanager).

### Disable Event Manager

To disable the event manager, call the `disableEventManager()` method.

```php
$translator->disableEventManager();
```

### Check Availability of Event Manager

To check the availability of the event manager, call the `isEventManagerEnabled()`
method.

```php
$translator->enableEventManager();

$result = $translator->isEventManagerEnabled(); // true
```

#### Default Value

The default value of this option is `false`.

### Get Event Manager

```php
$eventManager = $translator->getEventManager(); // instance of `Laminas\EventManager\EventManager`
```

> ### Automatic instantiation
> 
> The translator can create an event manager instance independently. If no custom
> event manager is set for the translator, the `getEventManager()` method
> returns this instance.

#### Default Value

The default value of this option is an instance of
`Laminas\EventManager\EventManager` class.

### Set Custom Event Manager

```php
$eventManager = Laminas\EventManager\EventManager();
$translator->setEventManager($eventManager);
```

## Attach Event Listener

A listener is attached to the event manager.

```php
$translator->getEventManager()->attach(
    Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION,
    static function (Laminas\EventManager\EventInterface $event) {
        // …
    }
);
```

## Event Target and Parameters

As target of the events the current instance of
`Laminas\I18n\Translator\Translator` is set.

For the event `Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION`
the following parameters are set:

* `message`
* `locale`
* `text_domain`

For the event `Laminas\I18n\Translator\Translator::EVENT_NO_MESSAGES_LOADED`:

* `locale`
* `text_domain`

## Example

> ### Installation requirements
>
> The following examples depends on the
> [laminas-log](https://docs.laminas.dev/laminas-log/)
> component, so be sure to have it installed before getting started:
>
> ```bash
> $ composer require laminas/laminas-log
> ```

The following example does not add any translations to demonstrate the logging.

```php
// Set locale
Locale::setDefault('de_DE');

// Create file logger
$writer = new Laminas\Log\Writer\Stream(__DIR__ . '/translator.log');
$logger = new Laminas\Log\Logger();
$logger->addWriter($writer);

// Create translator
$translator = new Laminas\I18n\Translator\Translator();

// Omit translations for demonstration

// Enable event manager
$translator->enableEventManager();

// Attach listeners
$translator->getEventManager()->attach(
    Laminas\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION,
    static function (Laminas\EventManager\EventInterface $event) use ($logger) {
        $logger->error('Missing translation', $event->getParams());
    }
);
$translator->getEventManager()->attach(
    Laminas\I18n\Translator\Translator::EVENT_NO_MESSAGES_LOADED,
    static function (Laminas\EventManager\EventInterface $event) use ($logger) {
        $logger->error('No messages loaded', $event->getParams());
    }
);

// Trigger event for no messages loaded and missing translation
echo $translator->translate('car'); 
```

This creates two entries in the log file:

```text
2020-03-20T21:00:30+00:00 ERR (3): No messages loaded {"locale":"de_DE","text_domain":"default"}
2020-03-20T21:00:30+00:00 ERR (3): Missing translation {"message":"car","locale":"de_DE","text_domain":"default"}
```

The concept of logging, creating logger with writer and to write messages can be
found in [documentation of laminas-log](https://docs.laminas.dev/laminas-log/).
