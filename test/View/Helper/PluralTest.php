<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\Plural as PluralHelper;
use LaminasTest\I18n\TestCase;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class PluralTest extends TestCase
{
    /** @var PluralHelper */
    public $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new PluralHelper();
    }

    /**
     * @return array
     */
    public function pluralsTestProvider()
    {
        return [
            ['nplurals=1; plural=0', 'かさ', 0, 'かさ'],
            ['nplurals=1; plural=0', 'かさ', 10, 'かさ'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 0, 'umbrellas'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 1, 'umbrella'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 2, 'umbrellas'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 0, 'parapluie'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 1, 'parapluie'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 2, 'parapluies'],
        ];
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
