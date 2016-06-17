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

use Es\Exception\ExceptionInterface;
use Es\Http\Response;
use Es\Http\Stream;
use Es\System\SystemEvent;
use Psr\Http\Message\RequestInterface;

/**
 * The strategy of json error handling.
 */
class JsonErrorStrategy extends AbstractErrorStrategy
{
    /**
     * Is the strategy acceptable for this request?
     *
     * @param \Psr\Http\Message\RequestInterface $request The request
     *
     * @return bool Returns true on success, false otherwise
     */
    public function isAcceptable(RequestInterface $request)
    {
        $accept = $request->getHeaderLine('Accept');
        if ($accept && preg_match('#^application/([^+\s]+\+)?json#', $accept)) {
            return true;
        }

        return false;
    }

    /**
     * Handles an error in development mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     * @param \Exception|\Error      $exception   The exception or the error
     */
    public function handleDevelopmentError(SystemEvent $systemEvent, $exception)
    {
        $details = [];

        $e = $exception;
        while ($e) {
            $item = [
                'class'   => get_class($exception),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString(),
            ];

            $details[] = $item;
            $e         = $e->getPrevious();
        }
        $result = json_encode(['details' => $details]);
        $body   = Stream::make($result);

        $status = 503;

        if ($exception instanceof ExceptionInterface && $exception->getCode()) {
            $status = $exception->getCode();
        }
        $response = new Response($status, $body, ['Content-Type' => 'application/problem+json']);
        $this->processResponse($response, $systemEvent);
    }

    /**
     * Handles "not found" error in production mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     */
    public function handleNotFoundError(SystemEvent $systemEvent)
    {
        $details  = ['details' => 'Resource not found'];
        $result   = json_encode($details);
        $body     = Stream::make($result);
        $response = new Response(404, $body, ['Content-Type' => 'application/problem+json']);
        $this->processResponse($response, $systemEvent);
    }

    /**
     * Handles a unexpected error in production mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     * @param \Exception|\Error      $exception   The exception or the error
     */
    public function handleProductionError(SystemEvent $systemEvent, $exception)
    {
        $details = ['details' => 'The resource is temporary unavailable'];
        $result  = json_encode($details);
        $body    = Stream::make($result);

        $status = 503;
        if ($exception instanceof ExceptionInterface && $exception->getCode()) {
            $status = $exception->getCode();
        }
        $response = new Response($status, $body, ['Content-Type' => 'application/problem+json']);
        $this->processResponse($response, $systemEvent);
    }
}
