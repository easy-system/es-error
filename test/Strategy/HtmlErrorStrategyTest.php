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

use Es\Error\Strategy\HtmlErrorStrategy;
use Es\Error\TemplateRenderer\ErrorRenderer;
use Es\Events\Events;
use Es\Http\Request;
use Es\Services\Services;
use Es\Services\ServicesTrait;
use Es\System\SystemEvent;
use Exception;
use Psr\Http\Message\ResponseInterface;

class HtmlErrorStrategyTest extends \PHPUnit_Framework_TestCase
{
    use ServicesTrait;

    public function setUp()
    {
        require_once 'FakeException.php';
    }

    public function testSetRenderer()
    {
        $services = new Services();
        $this->setServices($services);

        $renderer = new ErrorRenderer();
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $this->assertSame($renderer, $services->get('ErrorTemplateRenderer'));
    }

    public function testGetRenderer()
    {
        $renderer = new ErrorRenderer();
        $services = new Services();
        $services->set('ErrorTemplateRenderer', $renderer);

        $this->setServices($services);
        $strategy = new HtmlErrorStrategy();
        $this->assertSame($renderer, $strategy->getRenderer());
    }

    public function testIsAcceptable()
    {
        $strategy = new HtmlErrorStrategy();
        $this->assertTrue($strategy->isAcceptable(new Request()));
    }

    public function testHandleDevelopmentErrorWithDefaultStatus()
    {
        $renderer = $this->getMock(ErrorRenderer::CLASS);
        $events   = $this->getMock(Events::CLASS);
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new Exception('Foo');

        $content = 'Lorem ipsum dolor sit amet';
        $renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('error/development'),
                $this->identicalTo([
                    'exception' => $exception,
                    'eventName' => $event->getName(),
                ])
            )
            ->will($this->returnValue($content));

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleDevelopmentError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame($content, (string) $response->getBody());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame(503, $response->getStatusCode());
    }

    public function testHandleDevelopmentErrorWithSpecifiedStatus()
    {
        $renderer = $this->getMock(ErrorRenderer::CLASS);
        $events   = $this->getMock(Events::CLASS);
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new FakeException('Foo', 405);

        $content = 'Lorem ipsum dolor sit amet';
        $renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('error/development'),
                $this->identicalTo([
                    'exception' => $exception,
                    'eventName' => $event->getName(),
                ])
            )
            ->will($this->returnValue($content));

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleDevelopmentError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame($content, (string) $response->getBody());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame(405, $response->getStatusCode());
    }

    public function testHandleNotFoundError()
    {
        $renderer = $this->getMock(ErrorRenderer::CLASS);
        $events   = $this->getMock(Events::CLASS);
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $strategy->setEvents($events);

        $event = new SystemEvent('Foo');

        $content = 'Lorem ipsum dolor sit amet';
        $renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('error/404-error')
            )
            ->will($this->returnValue($content));

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleNotFoundError($event);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame($content, (string) $response->getBody());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testHandleProductionErrorWithDefaultStatus()
    {
        $renderer = $this->getMock(ErrorRenderer::CLASS);
        $events   = $this->getMock(Events::CLASS);
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new Exception('Foo');

        $content = 'Lorem ipsum dolor sit amet';
        $renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('error/production')
            )
            ->will($this->returnValue($content));

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleProductionError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame($content, (string) $response->getBody());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame(503, $response->getStatusCode());
    }

    public function testHandleProductionErrorWithSpecifiedStatus()
    {
        $renderer = $this->getMock(ErrorRenderer::CLASS);
        $events   = $this->getMock(Events::CLASS);
        $strategy = new HtmlErrorStrategy();
        $strategy->setRenderer($renderer);
        $strategy->setEvents($events);

        $event     = new SystemEvent('Foo');
        $exception = new FakeException('Foo', 405);

        $content = 'Lorem ipsum dolor sit amet';
        $renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('error/production')
            )
            ->will($this->returnValue($content));

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));

        $strategy->handleProductionError($event, $exception);

        $response = $event->getResult(SystemEvent::FINISH);
        $this->assertInstanceOf(ResponseInterface::CLASS, $response);
        $this->assertSame($content, (string) $response->getBody());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame(405, $response->getStatusCode());
    }
}
