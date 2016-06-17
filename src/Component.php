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

use Es\Component\ComponentInterface;
use Es\System\ErrorEvent;

/**
 * The system component.
 */
class Component implements ComponentInterface
{
    /**
     * The configuration of services.
     *
     * @var array
     */
    protected $servicesConfig = [
        'HtmlErrorStrategy'     => 'Es\Error\Strategy\HtmlErrorStrategy',
        'JsonErrorStrategy'     => 'Es\Error\Strategy\JsonErrorStrategy',
        'ErrorTemplateRenderer' => 'Es\Error\TemplateRenderer\ErrorRenderer',
        'ErrorTemplateResolver' => 'Es\Error\TemplateRenderer\ResolverFactory::make',
    ];

    /**
     * The configuration of listeners.
     *
     * @var array
     */
    protected $listenersConfig = [
        'ErrorListener'         => 'Es\Error\ErrorListenerFactory::make',
    ];

    /**
     * The configuration of events.
     *
     * @var array
     */
    protected $eventsConfig = [
        'ErrorListener::__invoke' => [
            ErrorEvent::FATAL_ERROR,
            'ErrorListener',
            '__invoke',
            1000,
        ],
    ];

    /**
     * The configuration of system.
     *
     * @var array
     */
    protected $systemConfig = [
        'error' => [
            'strategies' => [
                'html' => 'HtmlErrorStrategy',
                'json' => 'JsonErrorStrategy',
            ],
        ],
    ];

    /**
     * The current version of component.
     *
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * Gets the current version of component.
     *
     * @return string The version of component
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Gets the configuration of services.
     *
     * @return array The configuration of services
     */
    public function getServicesConfig()
    {
        return $this->servicesConfig;
    }

    /**
     * Gets the configuration of listeners.
     *
     * @return array The configuration of listeners
     */
    public function getListenersConfig()
    {
        return $this->listenersConfig;
    }

    /**
     * Gets the configuration of events.
     *
     * @return array The configuration of events
     */
    public function getEventsConfig()
    {
        return $this->eventsConfig;
    }

    /**
     * Gets the configuration of system.
     *
     * @return array The configuration of system
     */
    public function getSystemConfig()
    {
        return $this->systemConfig;
    }
}
