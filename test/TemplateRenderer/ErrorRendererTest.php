<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Test\TemplateRenderer;

use Error;
use Es\Error\TemplateRenderer\ErrorRenderer;
use Es\Error\TemplateRenderer\TemplateResolver;
use Es\Services\Services;
use Es\Services\ServicesTrait;
use Exception;

class ErrorRendererTest extends \PHPUnit_Framework_TestCase
{
    use ServicesTrait;

    protected $filesDir;

    public function setUp()
    {
        if (PHP_MAJOR_VERSION < 7 && ! class_exists('Error', false)) {
            require_once 'Error.php';
        }

        $this->filesDir = dirname(__DIR__)
                        . PHP_DS
                        . 'files'
                        . PHP_DS;
    }

    public function testSetResolver()
    {
        $services = new Services();
        $this->setServices($services);

        $resolver = new TemplateResolver();
        $renderer = new ErrorRenderer();
        $renderer->setResolver($resolver);
        $this->assertSame($resolver, $services->get('ErrorTemplateResolver'));
    }

    public function testGetResolver()
    {
        $resolver = new TemplateResolver();
        $services = new Services();
        $services->set('ErrorTemplateResolver', $resolver);

        $this->setServices($services);
        $renderer = new ErrorRenderer();
        $this->assertSame($resolver, $renderer->getResolver());
    }

    public function testRenderRenderTemplate()
    {
        $resolver = $this->getMock(TemplateResolver::CLASS);
        $renderer = new ErrorRenderer();
        $renderer->setResolver($resolver);

        $file = $this->filesDir . 'foo.phtml';

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo('foo'))
            ->will($this->returnValue($file));

        $this->assertSame('foo', $renderer->render('foo'));
    }

    public function testRenderRenderTemplateWithVariables()
    {
        $resolver = $this->getMock(TemplateResolver::CLASS);
        $renderer = new ErrorRenderer();
        $renderer->setResolver($resolver);

        $file      = $this->filesDir . 'template_variables.phtml';
        $variables = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo('foo'))
            ->will($this->returnValue($file));

        $this->assertSame('foobar', $renderer->render('foo', $variables));
    }

    public function testRenderRestoreBufferIfExceptionThrown()
    {
        $resolver = $this->getMock(TemplateResolver::CLASS);
        $renderer = new ErrorRenderer();
        $renderer->setResolver($resolver);

        $file = $this->filesDir . 'template_with_exception.phtml';

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo('foo'))
            ->will($this->returnValue($file));

        $bufferLevel = ob_get_level();
        try {
            $renderer->render('foo');
        } catch (Exception $ex) {
        }
        $this->assertSame(ob_get_level(), $bufferLevel);
    }

    public function testRenderRestoreBufferIfErrorThrown()
    {
        $resolver = $this->getMock(TemplateResolver::CLASS);
        $renderer = new ErrorRenderer();
        $renderer->setResolver($resolver);

        $file = $this->filesDir . 'template_with_error.phtml';

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo('foo'))
            ->will($this->returnValue($file));

        $bufferLevel = ob_get_level();
        try {
            $renderer->render('foo');
        } catch (Error $ex) {
        }
        $this->assertSame(ob_get_level(), $bufferLevel);
    }
}
