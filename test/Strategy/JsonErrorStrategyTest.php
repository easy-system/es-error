<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Test\Strategy;

use Es\Error\Strategy\JsonErrorStrategy;
use Es\Events\Events;
use Es\Http\Request;
use Es\System\SystemEvent;
use Exception;
use Psr\Http\Message\ResponseInterface;

class JsonErrorStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once 'FakeException.php';
    }

    public function testIsAcceptableOnFailure()
    {
        $request  = new Request();
        $strategy = new JsonErrorStrategy();
        $this->assertFalse($strategy->isAcceptable($request));
    }

    public function testIsAcceptableOnSuccess()
    {
        $request  = (new Request())->withHeader('Accept', 'application/json');
        $strategy = new JsonErrorStrategy();
        $this->assertTrue($strategy->isAcceptable($request));
    }

    public function testHandleDevelopmentErrorWithDefaultStatusCode()
    {
        $events   = $this->getMock(Events::CLASS);
        $strategy = new JsonErrorStrategy();
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new Exception('Foo');

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleDevelopmentError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertSame(503, $response->getStatusCode());
        $expected = [
            'details' => [
                [
                    'class'   => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'trace'   => $exception->getTraceAsString(),
                ],
            ],
        ];
        $this->assertSame($expected, json_decode((string) $response->getBody(), true));
    }

    public function testHandleDevelopmentErrorWithSpecifiedStatusCode()
    {
        $events   = $this->getMock(Events::CLASS);
        $strategy = new JsonErrorStrategy();
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new FakeException('Foo', 405);

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleDevelopmentError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertSame(405, $response->getStatusCode());
        $expected = [
            'details' => [
                [
                    'class'   => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'trace'   => $exception->getTraceAsString(),
                ],
            ],
        ];
        $this->assertSame($expected, json_decode((string) $response->getBody(), true));
    }

    public function testHandleNotFoundError()
    {
        $events   = $this->getMock(Events::CLASS);
        $strategy = new JsonErrorStrategy();
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new Exception('Foo');

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleNotFoundError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertSame(404, $response->getStatusCode());
        $expected = ['details' => 'Resource not found'];
        $this->assertSame($expected, json_decode((string) $response->getBody(), true));
    }

    public function testHandleProductionErrorWithDefaultStatusCode()
    {
        $events   = $this->getMock(Events::CLASS);
        $strategy = new JsonErrorStrategy();
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new Exception('Foo');

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleProductionError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertSame(503, $response->getStatusCode());
        $expected = ['details' => 'The resource is temporary unavailable'];
        $this->assertSame($expected, json_decode((string) $response->getBody(), true));
    }

    public function testHandleProductionErrorWithSpecifiedStatusCode()
    {
        $events   = $this->getMock(Events::CLASS);
        $strategy = new JsonErrorStrategy();
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new FakeException('Foo', 405);

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleProductionError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertSame(405, $response->getStatusCode());
        $expected = ['details' => 'The resource is temporary unavailable'];
        $this->assertSame($expected, json_decode((string) $response->getBody(), true));
    }
}
