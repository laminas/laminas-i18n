<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Laminas\I18n\Translator\TranslatorServiceFactory;
use PHPUnit_Framework_TestCase as TestCase;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined()
    {
        $slContents = array(array('Configuration', array()));
        $serviceLocator = $this->getMock('Laminas\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
                       ->method('get')
                       ->will($this->returnValueMap($slContents));

        $factory = new TranslatorServiceFactory();
        $translator = $factory->createService($serviceLocator);
        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
    }
}
