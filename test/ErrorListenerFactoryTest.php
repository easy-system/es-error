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

use Es\Services\Services;
use Es\Services\Provider;
use Es\System\SystemConfig;
use Es\Error\ErrorListenerFactory;
use Es\Error\ErrorListener;
use ReflectionProperty;
use Es\Error\Strategy\JsonErrorStrategy;

class ErrorListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $config = new SystemConfig;
        $config['error'] = [
            'strategies' => [
                'JsonErrorStrategy',
            ],
        ];
        $services = new Services;
        $services->set('Config', $config);
        $services->set('JsonErrorStrategy', JsonErrorStrategy::CLASS);

        Provider::setServices($services);

        $listener = ErrorListenerFactory::make();
        $this->assertInstanceOf(ErrorListener::CLASS, $listener);

        $reflection = new ReflectionProperty($listener, 'strategies');
        $reflection->setAccessible(true);
        $strategies = $reflection->getValue($listener);

        $strategies->rewind();
        $this->assertSame(1, count($strategies));
        $strategy = $strategies->current();
        $this->assertInstanceOf(JsonErrorStrategy::CLASS, $strategy);
    }
}
