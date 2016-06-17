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

use Es\Services\Provider;

/**
 * The factory of error listener.
 */
class ErrorListenerFactory
{
    /**
     * Makes the error listener.
     *
     * @return \Es\Error\ErrorListener The error listener
     */
    public static function make()
    {
        $services = Provider::getServices();
        $config = $services->get('Config');
        $strategies = [];
        if (isset($config['error']['strategies'])) {
            $strategies = (array) $config['error']['strategies'];
        }
        $listener = new ErrorListener;
        foreach ($strategies as $name) {
            $strategy = $services->get($name);
            $listener->attachErrorStrategy($strategy);
        }

        return $listener;
    }
}
