<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Test\TemplateRenderer;

use Es\Error\TemplateRenderer\ResolverFactory;
use Es\Error\TemplateRenderer\TemplateResolverInterface;
use Es\Services\Services;
use Es\Services\ServicesTrait;
use Es\System\SystemConfig;

class ResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ServicesTrait;

    protected $filesDir;

    public function setUp()
    {
        $this->filesDir = dirname(__DIR__)
                        . PHP_DS
                        . 'files'
                        . PHP_DS;
    }

    public function testMake()
    {
        $file            = $this->filesDir . 'development.phtml';
        $config          = new SystemConfig();
        $config['error'] = [
            'html' => [
                'templates' => [
                    'error/development' => $file,
                ],
            ],
        ];
        $services = new Services();
        $services->set('Config', $config);
        $this->setServices($services);
        $resolver = ResolverFactory::make();
        $this->assertInstanceOf(TemplateResolverInterface::CLASS, $resolver);
        $this->assertSame($file, $resolver->resolve('error/development'));
    }
}
