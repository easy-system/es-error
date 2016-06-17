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

use InvalidArgumentException;
use SplFileInfo;
use UnexpectedValueException;

/**
 * Resolver of error templates.
 */
class TemplateResolver implements TemplateResolverInterface
{
    /**
     * The default error templates.
     *
     * @var array
     */
    protected $templates = [
        'error/development' => 'error/development.phtml',
        'error/production'  => 'error/production.phtml',
        'error/404-error'   => 'error/404-error.phtml',
    ];

    /**
     * Sets template.
     *
     * @param string $name The name of template
     * @param string $path The path to template
     *
     * @throws \UnexpectedValueException If non-standart template name specified
     * @throws \InvalidArgumentException
     *
     * - If specified template file not exists
     * - If the extension of template file is not "phtml"
     */
    public function setTemplate($name, $path)
    {
        if (! isset($this->templates[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Unexpected template name "%s" provided.',
                is_string($name) ? $name : gettype($name)
            ));
        }

        $finfo    = new SplFileInfo($path);
        $realPath = $finfo->getRealPath();
        if (! $realPath) {
            throw new InvalidArgumentException(sprintf(
                'Invalid path "%s" of template "%s" provided.',
                $path,
                $name
            ));
        }
        $extension = $finfo->getExtension();
        if ('phtml' !== $extension) {
            throw new InvalidArgumentException(sprintf(
                'Invalid file extension "%s" of template "%s"; the extension '
                . 'of template file must be "phtml".',
                $extension,
                $name
            ));
        }
        $this->templates[$name] = $realPath;
    }

    /**
     * Gets the current configuration of templates.
     *
     * @return array The configuration of templates
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Resolves template.
     *
     * @param string $template The name of template
     *
     * @throws \UnexpectedValueException If non-standart template name specified
     *
     * @return string The path to template
     */
    public function resolve($template)
    {
        if (! isset($this->templates[$template])) {
            throw new UnexpectedValueException(sprintf(
                'Unexpected template name "%s" provided.',
                is_string($template) ? $template : gettype($template)
            ));
        }

        return $this->templates[$template];
    }
}
