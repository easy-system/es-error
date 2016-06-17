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

/**
 * The interface of error renderer.
 */
interface ErrorRendererInterface
{
    /**
     * Sets the resolver of error templates.
     *
     * @param TemplateResolverInterface $resolver The resolver
     */
    public function setResolver(TemplateResolverInterface $resolver);

    /**
     * Gets the resolver of error templates.
     *
     * @return TemplateResolverInterface The resolver
     */
    public function getResolver();

    /**
     * Renders template.
     *
     * @param string $template  The name of error template
     * @param array  $variables Optional; the variables to rendering
     *
     * @throws \Exception Translates any exception thrown during the rendering
     * @throws \Error     Translates any error thrown during the rendering
     *
     * @return string The string representation of error
     */
    public function render($template, array $variables = []);
}
