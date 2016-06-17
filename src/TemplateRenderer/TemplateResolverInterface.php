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
 * Interface of template resolver.
 */
interface TemplateResolverInterface
{
    /**
     * Sets template.
     *
     * @param string $name The name of template
     * @param string $path The path to template
     */
    public function setTemplate($name, $path);

    /**
     * Gets the current configuration of templates.
     *
     * @return array The configuration of templates
     */
    public function getTemplates();

    /**
     * Resolves template.
     *
     * @param string $template The name of template
     *
     * @return string The path to template
     */
    public function resolve($template);
}
