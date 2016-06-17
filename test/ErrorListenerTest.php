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

use Es\Error\ErrorListener;
use Es\Error\Strategy\HtmlErrorStrategy;
use Es\Error\Strategy\JsonErrorStrategy;
use Es\Server\Server;
use Es\Services\Services;
use Es\System\ErrorEvent;
use Es\System\System;
use Exception;
use ReflectionProperty;

class ErrorListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once 'SystemTestHelper.php';
        require_once 'FakeNotFoundException.php';
    }

    public function testSetServer()
    {
        $server   = new Server();
        $listener = new ErrorListener();
        $listener->setServer($server);
        $this->assertSame($server, $listener->getServer());
    }

    public function testGetServer()
    {
        $server   = new Server();
        $services = new Services();
        $services->set('Server', $server);

        $listener = new ErrorListener();
        $listener->setServices($services);
        $this->assertSame($server, $listener->getServer());
    }

    public function testSetDefaultErrorStrategy()
    {
        $strategy = new HtmlErrorStrategy();
        $listener = new ErrorListener();
        $listener->setDefaultErrorStrategy($strategy);
        $this->assertSame($strategy, $listener->getDefaultErrorStrategy());
    }

    public function testGetDefaultErrorStrategy()
    {
        $strategy = new HtmlErrorStrategy();
        $services = new Services();
        $services->set('HtmlErrorStrategy', $strategy);

        $listener = new ErrorListener();
        $listener->setServices($services);
        $this->assertSame($strategy, $listener->getDefaultErrorStrategy());
    }

    public function testAttachErrorStrategy()
    {
        $strategy = new JsonErrorStrategy();
        $listener = new ErrorListener();
        $listener->attachErrorStrategy($strategy);

        $reflection = new ReflectionProperty($listener, 'strategies');
        $reflection->setAccessible(true);
        $strategies = $reflection->getValue($listener);

        $this->assertInstanceOf('SplObjectStorage', $strategies);
        $this->assertTrue($strategies->contains($strategy));
    }

    public function testAttachErrorStrategyWithHtmlStrategySetsDefaultStrategy()
    {
        $strategy = new HtmlErrorStrategy();
        $listener = new ErrorListener();
        $listener->attachErrorStrategy($strategy);
        $this->assertSame($strategy, $listener->getDefaultErrorStrategy());
    }

    public function testInvokeCallJsonErrorStrategyToProcessDevelopmentError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], true);
        $exception   = new Exception();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server  = new Server();
        $request = $server->getRequest()->withHeader('Accept', 'application/json');
        $server->setRequest($request);

        $strategy = $this->getMock(JsonErrorStrategy::CLASS, ['handleDevelopmentError']);

        $listener = new ErrorListener();
        $listener->attachErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleDevelopmentError')
            ->with(
                $this->identicalTo($systemEvent),
                $this->identicalTo($exception)
            );

        $listener($errorEvent);
    }

    public function testInvokeCallJsonErrorStrategyToProcessNotFoundError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], false);
        $exception   = new FakeNotFoundException();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server  = new Server();
        $request = $server->getRequest()->withHeader('Accept', 'application/json');
        $server->setRequest($request);

        $strategy = $this->getMock(JsonErrorStrategy::CLASS, ['handleNotFoundError']);

        $listener = new ErrorListener();
        $listener->attachErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleNotFoundError')
            ->with(
                $this->identicalTo($systemEvent)
            );

        $listener($errorEvent);
    }

    public function testInvokeCallJsonErrorStrategyToProcessProductionError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], false);
        $exception   = new Exception();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server  = new Server();
        $request = $server->getRequest()->withHeader('Accept', 'application/json');
        $server->setRequest($request);

        $strategy = $this->getMock(JsonErrorStrategy::CLASS, ['handleProductionError']);

        $listener = new ErrorListener();
        $listener->attachErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleProductionError')
            ->with(
                $this->identicalTo($systemEvent),
                $this->identicalTo($exception)
            );

        $listener($errorEvent);
    }

    public function testInvokeCallHtmlErrorStrategyToProcessDevelopmentError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], true);
        $exception   = new Exception();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server = new Server();

        $strategy = $this->getMock(HtmlErrorStrategy::CLASS);

        $listener = new ErrorListener();
        $listener->setDefaultErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleDevelopmentError')
            ->with(
                $this->identicalTo($systemEvent),
                $this->identicalTo($exception)
            );

        $listener($errorEvent);
    }

    public function testInvokeCallHtmlErrorStrategyToProcessNotFoundError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], false);
        $exception   = new FakeNotFoundException();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server = new Server();

        $strategy = $this->getMock(HtmlErrorStrategy::CLASS);

        $listener = new ErrorListener();
        $listener->setDefaultErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleNotFoundError')
            ->with(
                $this->identicalTo($systemEvent)
            );

        $listener($errorEvent);
    }

    public function testInvokeCallHtmlErrorStrategyToProcessProductionError()
    {
        SystemTestHelper::resetSystem();
        $system      = System::init([], false);
        $exception   = new Exception();
        $systemEvent = $system->getEvent();
        $errorEvent  = new ErrorEvent(ErrorEvent::FATAL_ERROR, $exception, $system);

        $server = new Server();

        $strategy = $this->getMock(HtmlErrorStrategy::CLASS);

        $listener = new ErrorListener();
        $listener->setDefaultErrorStrategy($strategy);
        $listener->setServer($server);

        $strategy
            ->expects($this->once())
            ->method('handleProductionError')
            ->with(
                $this->identicalTo($systemEvent),
                $this->identicalTo($exception)
            );

        $listener($errorEvent);
    }
}
