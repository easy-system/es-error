<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\TemplateRenderer;

use Es\Services\Provider;

/**
 * The factory of template resolver.
 */
class ResolverFactory
{
    /**
     * Makes the resolver.
     *
     * @return TemplateResolver The resolver
     */
    public static function make()
    {
        $resolver = new TemplateResolver();

        $services = Provider::getServices();
        $config   = $services->get('Config');

        if (isset($config['error']['html']['templates'])) {
            $templates = (array) $config['error']['html']['templates'];
            foreach ($templates as $template => $path) {
                $resolver->setTemplate($template, $path);
            }
        }

        return $resolver;
    }
}
