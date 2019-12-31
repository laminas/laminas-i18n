<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\Translator;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use PHPUnit_Framework_TestCase as TestCase;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined()
    {
        $slContents = [['config', []]];
        $serviceLocator = $this->getMock(ContainerInterface::class);
        $serviceLocator->expects($this->once())
                       ->method('get')
                       ->will($this->returnValueMap($slContents));

        $factory = new TranslatorServiceFactory();
        $translator = $factory($serviceLocator, 'Laminas\I18n\Translator\Translator');
        $this->assertInstanceOf('Laminas\I18n\Translator\Translator', $translator);
    }
}
