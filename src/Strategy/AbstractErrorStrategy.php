<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Strategy;

use Es\Events\EventsTrait;
use Es\Server\ServerTrait;
use Es\System\SystemEvent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Abstract strategy of error handling.
 */
abstract class AbstractErrorStrategy
{
    use EventsTrait, ServerTrait;

    /**
     * Is the strategy acceptable for this request?
     *
     * @param \Psr\Http\Message\RequestInterface $request The request
     *
     * @return bool Returns true on success, false otherwise
     */
    abstract public function isAcceptable(RequestInterface $request);

    /**
     * Handles an error in development mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     * @param \Exception|\Error      $exception   The exception or the error
     */
    abstract public function handleDevelopmentError(SystemEvent $systemEvent, $exception);

    /**
     * Handles "not found" error in production mode.
     */
    abstract public function handleNotFoundError(SystemEvent $systemEvent);

    /**
     * Handles a unexpected error in production mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     * @param \Exception|\Error      $exception   The exception or the error
     */
    abstract public function handleProductionError(SystemEvent $systemEvent, $exception);

    /**
     * Processes the response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The response
     * @param \Es\System\SystemEvent              $event    The system event
     */
    public function processResponse(ResponseInterface $response, SystemEvent $event)
    {
        if ($event->getName() === SystemEvent::FINISH) {
            $event->stopPropagation(true);
            $server  = $this->getServer();
            $emitter = $server->getEmitter();
            $emitter->emit($response);

            return;
        }
        $events = $this->getEvents();
        $event->setResult(SystemEvent::FINISH, $response);
        $events->trigger($event(SystemEvent::FINISH));
    }
}
