<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error;

use Es\Error\Strategy\AbstractErrorStrategy;
use Es\Error\Strategy\HtmlErrorStrategy;
use Es\Exception\NotFoundExceptionInterface;
use Es\Server\ServerTrait;
use Es\Services\ServicesTrait;
use Es\System\ErrorEvent;
use SplObjectStorage;

/**
 * Handles a fatal system errors.
 */
class ErrorListener
{
    use ServerTrait, ServicesTrait;

    /**
     * The strategy of html error handling.
     *
     * @var \Es\Error\Strategy\HtmlErrorStrategy
     */
    protected $defaultErrorStrategy;

    /**
     * The available strategies.
     *
     * @var \SplObjectStorage
     */
    protected $strategies;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->strategies = new SplObjectStorage();
    }

    /**
     * Sets the strategy of html error handling.
     *
     * @var \Es\Error\Strategy\HtmlErrorStrategy The strategy
     */
    public function setDefaultErrorStrategy(HtmlErrorStrategy $strategy)
    {
        $this->defaultErrorStrategy = $strategy;
    }

    /**
     * Gets the strategy of html error handling.
     *
     * @return \Es\Error\Strategy\HtmlErrorStrategy The strategy
     */
    public function getDefaultErrorStrategy()
    {
        if (! $this->defaultErrorStrategy) {
            $services = $this->getServices();
            $strategy = $services->get('HtmlErrorStrategy');
            $this->setDefaultErrorStrategy($strategy);
        }

        return $this->defaultErrorStrategy;
    }

    /**
     * Adds the specific strategy of error handling.
     *
     * @param \Es\Error\Strategy\AbstractErrorStrategy $strategy The strategy
     */
    public function attachErrorStrategy(AbstractErrorStrategy $strategy)
    {
        if ($strategy instanceof HtmlErrorStrategy) {
            $this->setDefaultErrorStrategy($strategy);

            return;
        }
        $this->strategies->attach($strategy);
    }

    /**
     * Handles a fatal system errors.
     *
     * @param \Es\System\ErrorEvent $event The error event
     */
    public function __invoke(ErrorEvent $event)
    {
        $server  = $this->getServer();
        $request = $server->getRequest();

        $strategy = null;

        foreach ($this->strategies as $item) {
            if ($item->isAcceptable($request)) {
                $strategy = $item;

                break;
            }
        }

        if (null === $strategy) {
            $strategy = $this->getDefaultErrorStrategy();
        }

        $system      = $event->getContext();
        $systemEvent = $system->getEvent();
        $exception   = $event->getException();

        if ($system->isDevMode()) {
            $strategy->handleDevelopmentError($systemEvent, $exception);
        } elseif ($exception instanceof NotFoundExceptionInterface) {
            $strategy->handleNotFoundError($systemEvent);
        } else {
            $strategy->handleProductionError($systemEvent, $exception);
        }
    }
}
