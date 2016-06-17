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
use Es\Events\Events;
use Es\Http\Response;
use Es\Http\Response\SapiEmitter;
use Es\Server\Server;
use Es\System\SystemEvent;

class AbstractErrorStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessResponseEmitResponseIfTheSystemEventIsAtTheFinish()
    {
        $emitter = $this->getMock(SapiEmitter::CLASS);
        $server  = new Server();
        $server->setEmitter($emitter);

        $strategy = new HtmlErrorStrategy();
        $strategy->setServer($server);

        $event    = new SystemEvent(SystemEvent::FINISH);
        $response = new Response();

        $emitter
            ->expects($this->once())
            ->method('emit')
            ->with($this->identicalTo($response));

        $strategy->processResponse($response, $event);
    }

    public function testProcessResponseTriggerFinishEventIfTheSystemEventIsNotAtTheFinish()
    {
        $server = new Server();
        $events = $this->getMock(Events::CLASS);

        $strategy = new HtmlErrorStrategy();
        $strategy->setServer($server);
        $strategy->setEvents($events);

        $event    = new SystemEvent();
        $response = new Response();

        $events
            ->expects($this->once())
            ->method('trigger')
            ->with($this->callback(function ($systemEvent) use ($event, $response) {
                $this->assertSame($systemEvent, $event);
                $this->assertSame($response, $systemEvent->getResult(SystemEvent::FINISH));

                return true;
            }));

        $strategy->processResponse($response, $event);
    }
}
