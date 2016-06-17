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

use Es\Error\TemplateRenderer\ErrorRendererInterface;
use Es\Exception\ExceptionInterface;
use Es\Http\Response;
use Es\Http\Stream;
use Es\Services\Provider;
use Es\System\SystemEvent;
use Psr\Http\Message\RequestInterface;

/**
 * The strategy of html error handling.
 */
class HtmlErrorStrategy extends AbstractErrorStrategy
{
    /**
     * Sets the renderer.
     *
     * @param \Es\Error\TemplateRenderer\ErrorRendererInterface $renderer The renderer
     */
    public function setRenderer(ErrorRendererInterface $renderer)
    {
        Provider::getServices()->set('ErrorTemplateRenderer', $renderer);
    }

    /**
     * Gets the renderer.
     *
     * @return \Es\Error\TemplateRenderer\ErrorRendererInterface The renderer
     */
    public function getRenderer()
    {
        return Provider::getServices()->get('ErrorTemplateRenderer');
    }

    /**
     * Is the strategy acceptable for this request?
     *
     * @param \Psr\Http\Message\RequestInterface $request The request
     *
     * @return bool Returns true on success, false otherwise
     */
    public function isAcceptable(RequestInterface $request)
    {
        return true;
    }

    /**
     * Handles an error in development mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     * @param \Exception|\Error      $exception   The exception or the error
     */
    public function handleDevelopmentError(SystemEvent $systemEvent, $exception)
    {
        $options = [
            'exception' => $exception,
            'eventName' => $systemEvent->getName(),
        ];
        $renderer = $this->getRenderer();
        $result   = $renderer->render('error/development', $options);
        $body     = Stream::make($result);

        $status = 503;
        if ($exception instanceof ExceptionInterface && $exception->getCode()) {
            $status = $exception->getCode();
        }
        $response = new Response($status, $body, ['Content-Type' => 'text/html']);
        $this->processResponse($response, $systemEvent);
    }

    /**
     * Handles "not found" error in production mode.
     *
     * @param \Es\System\SystemEvent $systemEvent The system event
     */
    public function handleNotFoundError(SystemEvent $systemEvent)
    {
        $renderer = $this->getRenderer();
        $result   = $renderer->render('error/404-error');
        $body     = Stream::make($result);
        $response = new Response(404, $body, ['Content-Type' => 'text/html']);
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
        $renderer = $this->getRenderer();
        $result   = $renderer->render('error/production');
        $body     = Stream::make($result);

        $status = 503;
        if ($exception instanceof ExceptionInterface && $exception->getCode()) {
            $status = $exception->getCode();
        }
        $response = new Response($status, $body, ['Content-Type' => 'text/html']);
        $this->processResponse($response, $systemEvent);
    }
}
