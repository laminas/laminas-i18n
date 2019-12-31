<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\TextDomain;
use Laminas\I18n\Translator\Translator;
use LaminasTest\I18n\Translator\TestAsset\Loader as TestLoader;
use Locale;
use PHPUnit_Framework_TestCase as TestCase;

class TranslatorTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $originalLocale;

    /**
     * @var string
     */
    protected $testFilesDir;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        $this->translator     = new Translator();

        Locale::setDefault('en_EN');

        $this->testFilesDir = __DIR__ . '/_files';
    }

    public function tearDown()
    {
        Locale::setDefault($this->originalLocale);
    }

    public function testFactoryCreatesTranslator()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'patterns' => array(
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                )
            ),
            'files' => array(
                array(
                    'type' => 'phparray',
                    'filename' => $this->testFilesDir . '/translation_en.php',
                )
            )
        ));

        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
        $this->assertEquals('de_DE', $translator->getLocale());
    }

    public function testFactoryCreatesTranslatorWithCache()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'patterns' => array(
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                )
            ),
            'cache' => array(
                'adapter' => 'memory'
            )
        ));

        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
        $this->assertInstanceOf('Laminas\Cache\Storage\StorageInterface', $translator->getCache());
    }

    public function testDefaultLocale()
    {
        $this->assertEquals('en_EN', $this->translator->getLocale());
    }

    public function testForcedLocale()
    {
        $this->translator->setLocale('de_DE');
        $this->assertEquals('de_DE', $this->translator->getLocale());
    }

    public function testTranslate()
    {
        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(array('foo' => 'bar'));
        $this->translator->getPluginManager()->setService('test', $loader);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }


    public function testTranslateWithCache()
    {
        $cache = \Laminas\Cache\StorageFactory::factory(array('adapter' => 'memory'));
        $this->translator->setCache($cache);

        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(array('foo' => 'bar'));
        $this->translator->getPluginManager()->setService('test', $loader);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslatePlurals()
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $pl0 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        $this->assertEquals('Message 5 (en) Plural 0', $pl0);
        $this->assertEquals('Message 5 (en) Plural 1', $pl1);
        $this->assertEquals('Message 5 (en) Plural 2', $pl2);
    }
}
