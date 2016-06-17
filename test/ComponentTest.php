<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Test;

use Es\Error\Component;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    protected $requiredServices = [
        'HtmlErrorStrategy',
        'JsonErrorStrategy',
        'ErrorTemplateRenderer',
        'ErrorTemplateResolver',
    ];

    protected $requiredStrategies = [
        'html',
        'json',
    ];

    public function testGetVersion()
    {
        $component = new Component();
        $version   = $component->getVersion();
        $this->assertInternalType('string', $version);
        $this->assertRegExp('#\d+.\d+.\d+#', $version);
    }

    public function testGetServicesConfig()
    {
        $component = new Component();
        $config    = $component->getServicesConfig();
        $this->assertInternalType('array', $config);
        foreach ($this->requiredServices as $item) {
            $this->assertArrayHasKey($item, $config);
        }
    }

    public function testGetListenersConfig()
    {
        $component = new Component();
        $config    = $component->getListenersConfig();
        $this->assertInternalType('array', $config);
    }

    public function testGetEventsConfig()
    {
        $component = new Component();
        $config    = $component->getEventsConfig();
        $this->assertInternalType('array', $config);
    }

    public function testGetSystemConfig()
    {
        $component    = new Component();
        $systemConfig = $component->getSystemConfig();
        $this->assertInternalType('array', $systemConfig);
        $this->assertArrayHasKey('error', $systemConfig);

        $errorConfig = $systemConfig['error'];
        $this->assertInternalType('array', $errorConfig);
        $this->assertArrayHasKey('strategies', $errorConfig);

        $strategiesConfig = $errorConfig['strategies'];
        $this->assertInternalType('array', $strategiesConfig);

        foreach ($this->requiredStrategies as $item) {
            $this->assertArrayHasKey($item, $strategiesConfig);
        }
    }
}
