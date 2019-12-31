<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\View\Helper\Plural as PluralHelper;

/**
 * @category   Laminas
 * @package    Laminas_View
 * @subpackage UnitTests
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class PluralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PluralHelper
     */
    public $helper;

    /**
     * Sets up the fixture
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new PluralHelper();
    }

    /**
     * @return array
     */
    public function pluralsTestProvider()
    {
        return array(
            array('nplurals=1; plural=0', 'かさ', 0, 'かさ'),
            array('nplurals=1; plural=0', 'かさ', 10, 'かさ'),

            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 0, 'umbrellas'),
            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 1, 'umbrella'),
            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 2, 'umbrellas'),

            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 0, 'parapluie'),
            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 1, 'parapluie'),
            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 2, 'parapluies'),
        );
    }

    /**
     * @dataProvider pluralsTestProvider
     */
    public function testGetCorrectPlurals($pluralRule, $strings, $number, $expected)
    {
        $this->helper->setPluralRule($pluralRule);
        $result = $this->helper->__invoke($strings, $number);
        $this->assertEquals($expected, $result);
    }
}
