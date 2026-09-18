<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Core', [
        'src/CountryCode.php',
        'src/DefaultLocale.php',
        'src/I18nDefaults.php',
    ])
    ->layer('Config', 'src/ConfigProvider.php')
    ->layer('Exception', 'src/Exception')
    ->layer('Factory', 'src/Factory')
    ->layer('Geography', 'src/Geography')
    ->layer('Plural', 'src/Translator/Plural')
    ->layer('TextDomain', 'src/Translator/TextDomain.php')
    ->layer('Event', 'src/Translator/Event')
    ->layerPattern(
        'Loader',
        '/^Laminas\\\\I18n\\\\Translator\\\\Loader\\\\.*$/',
        '/^Laminas\\\\I18n\\\\Translator\\\\Loader\\\\Factory\\\\.*$/'
    )
    ->layer('LoaderFactory', 'src/Translator/Loader/Factory')
    ->layer('LoaderPluginManager', [
        'src/Translator/LoaderPluginManager.php',
        'src/Translator/LoaderPluginManagerFactory.php',
        'src/Translator/MessageLoaderPluginManagerInterface.php',
    ])
    ->layer('Value', 'src/Translator/Value')
    ->layerPattern(
        'TranslationCollector',
        '/^Laminas\\\\I18n\\\\Translator\\\\TranslationCollector\\\\.*$/',
        '/^Laminas\\\\I18n\\\\Translator\\\\TranslationCollector\\\\Factory\\\\.*$/'
    )
    ->layer('TranslationCollectorFactory', 'src/Translator/TranslationCollector/Factory')
    ->layer('Translator', [
        'src/Translator/Translator.php',
        'src/Translator/TranslatorServiceFactory.php',
    ])
    ->ruleset([
        'Exception'                   => [],
        'Core'                        => ['Exception'],
        'Factory'                     => ['Core'],
        'Geography'                   => ['Core'],
        'Plural'                      => ['Exception'],
        'TextDomain'                  => ['+Plural'],
        'Event'                       => ['TextDomain'],
        'Loader'                      => ['+TextDomain'],
        'LoaderFactory'               => ['Exception', 'Loader'],
        'LoaderPluginManager'         => ['Loader', 'LoaderFactory'],
        'Value'                       => ['Exception', 'Loader'],
        'TranslationCollector'        => ['LoaderPluginManager', 'TextDomain', 'Translator', '+Value'],
        'TranslationCollectorFactory' => ['+Core', 'LoaderPluginManager', 'TranslationCollector', 'Value'],
        'Translator'                  => ['+Core', '+Event', 'TranslationCollector'],
        'Config'                      => [
            '+Factory',
            '+Geography',
            '+LoaderPluginManager',
            'TranslationCollector',
            'TranslationCollectorFactory',
            'Translator',
            'Value',
        ],
    ]);
