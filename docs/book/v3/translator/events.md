# Events

The translator of laminas-i18n dispatches two events during the processing of
translations:

- `Laminas\I18n\Translator\Event\MissingTranslationEvent`
- `Laminas\I18n\Translator\Event\NoMessagesLoadedEvent`

The typical usage for these events is to log missing translations and track when
the loading of messages fails.

MISSING: **Installation Requirements**
The event support of laminas-i18n depends on a [PSR-14](https://www.php-fig.org/psr/psr-14/) compatible event dispatcher.
Choose any PSR-14 compatible event dispatcher.

## Basic Usage

```php
use Laminas\I18n\Translator\Event\MissingTranslationEvent;
use Laminas\I18n\Translator\Translator;

// Set locale
Locale::setDefault('de_DE');

// Create translator with a PSR-14 dispatcher
// $dispatcher = ...;
$translator = new Translator($collector, 'de_DE', null, 'default', $dispatcher);

// In your dispatcher configuration, you can now listen to the events
// Example with a hypothetical dispatcher:
$dispatcher->addListener(MissingTranslationEvent::class, static function (MissingTranslationEvent $event) {
    var_dump($event->getMessage());
    // 'car'
    var_dump($event->getLocale());
    // 'de_DE'
    var_dump($event->getTextDomain());
    // 'default'
});

// Trigger related events
echo $translator->translate('car');
```

## Using Event Dispatcher

### Enable Event Dispatcher

To enable the event dispatcher, call the `enableEventManager()` method.

```php
$translator->enableEventManager();
```

The event dispatcher can also be [enabled per configuration](configuration.md#event-dispatcher).

### Disable Event Dispatcher

To disable the event dispatcher, call the `disableEventManager()` method.

```php
$translator->disableEventManager();
```

### Check Availability of Event Dispatcher

To check the availability of the event dispatcher, call the `isEventManagerEnabled()`
method.

```php
$translator->enableEventManager();

$result = $translator->isEventManagerEnabled(); // true
```

#### Default Value

The default value of this option is `false`.

### Get Event Dispatcher

```php
$eventDispatcher = $translator->getEventDispatcher(); // returns Psr\EventDispatcher\EventDispatcherInterface or null
```

## Events

### MissingTranslationEvent

Fired when the translation for a message is missing.

Available methods:

- `getMessage()`: returns the message string.
- `getLocale()`: returns the locale string.
- `getTextDomain()`: returns the text domain string.
- `setTranslation(string $translation)`: allow providing a translation dynamically.
- `getTranslation()`: returns the translation if set.

### NoMessagesLoadedEvent

Fired when no messages were loaded for a locale/text-domain combination.

Available methods:

- `getLocale()`: returns the locale string.
- `getTextDomain()`: returns the text domain string.
- `setMessages(TextDomain $messages)`: allow providing messages dynamically.
- `getMessages()`: returns the messages if set.

## Example

The following example shows how to log missing translations.

```php
use Laminas\I18n\Translator\Event\MissingTranslationEvent;
use Laminas\I18n\Translator\Event\NoMessagesLoadedEvent;

// ... dispatcher configuration ...
$dispatcher->addListener(MissingTranslationEvent::class, static function (MissingTranslationEvent $event) use ($logger) {
    $logger->error('Missing translation', [
        'message'     => $event->getMessage(),
        'locale'      => $event->getLocale(),
        'text_domain' => $event->getTextDomain(),
    ]);
});

$dispatcher->addListener(NoMessagesLoadedEvent::class, static function (NoMessagesLoadedEvent $event) use ($logger) {
    $logger->error('No messages loaded', [
        'locale'      => $event->getLocale(),
        'text_domain' => $event->getTextDomain(),
    ]);
});

// Trigger event for no messages loaded and missing translation
echo $translator->translate('car'); // 'car'
```
