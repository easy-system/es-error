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

use Error;
use Es\Services\Provider;
use Exception;

/**
 * The simple renderer of error templates.
 */
class ErrorRenderer implements ErrorRendererInterface
{
    /**
     * Sets the resolver of error templates.
     *
     * @param TemplateResolverInterface $resolver The resolver
     */
    public function setResolver(TemplateResolverInterface $resolver)
    {
        Provider::getServices()->set('ErrorTemplateResolver', $resolver);
    }

    /**
     * Gets the resolver of error templates.
     *
     * @return TemplateResolverInterface The resolver
     */
    public function getResolver()
    {
        return Provider::getServices()->get('ErrorTemplateResolver');
    }

    /**
     * Renders template.
     *
     * @param string $__template  The name of error template
     * @param array  $__variables Optional; the variables to rendering
     *
     * @throws \Exception Translates any exception thrown during the rendering
     * @throws \Error     Translates any error thrown during the rendering
     *
     * @return string The string representation of error
     */
    public function render($__template, array $__variables = [])
    {
        extract($__variables);

        try {
            ob_start();
            include $this->getResolver()->resolve($__template);
            $__return = ob_get_clean();
        } catch (Error $__error) {
            ob_end_clean();
            throw $__error;
        } catch (Exception $__exception) {
            ob_end_clean();
            throw $__exception;
        }

        return $__return;
    }
}
