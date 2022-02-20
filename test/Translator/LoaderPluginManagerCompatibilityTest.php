<?php

namespace LaminasTest\I18n\Translator;

use Laminas\Filter\FilterInterface;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use LaminasTest\I18n\TestCase;

class LoaderPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected function getPluginManager()
    {
        return new LoaderPluginManager(new ServiceManager());
    }

    protected function getV2InvalidPluginException()
    {
        return RuntimeException::class;
    }

    protected function getInstanceOf()
    {
        return FilterInterface::class;
    }

    public function testInstanceOfMatches()
    {
        $this->markTestSkipped('Test skipped as LoaderPluginManager allows multiple instance types');
    }
}
