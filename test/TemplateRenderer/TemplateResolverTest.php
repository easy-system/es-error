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

use Es\Error\TemplateRenderer\TemplateResolver;

class TemplateResolverTest extends \PHPUnit_framework_TestCase
{
    protected $filesDir;

    public function setUp()
    {
        $this->filesDir = dirname(__DIR__) . PHP_DS . 'files' . PHP_DS;
    }

    public function testGetTemplates()
    {
        $resolver  = new TemplateResolver();
        $templates = $resolver->getTemplates();
        $this->assertInternalType('array', $templates);
        $this->assertArrayHasKey('error/development', $templates);
        $this->assertArrayHasKey('error/production',  $templates);
        $this->assertArrayHasKey('error/404-error',   $templates);
    }

    public function testSetTemplateOnSuccess()
    {
        $resolver = new TemplateResolver();
        $file     = $this->filesDir . 'development.phtml';
        $resolver->setTemplate('error/development', $file);
        $templates = $resolver->getTemplates();
        $this->assertInternalType('array', $templates);
        $this->assertSame($file, $templates['error/development']);
    }

    public function testSetTemplateRaiseExceptionIfNonStandartTemplateNameSpecified()
    {
        $resolver = new TemplateResolver();
        $file     = $this->filesDir . 'development.phtml';
        $this->setExpectedException('UnexpectedValueException');
        $resolver->setTemplate('foo', $file);
    }

    public function testSetTemplateRaiseExceptionIfNonExistentPathSpecified()
    {
        $resolver = new TemplateResolver();
        $file     = $this->filesDir . 'non-existent.phtml';
        $this->setExpectedException('InvalidArgumentException');
        $resolver->setTemplate('error/development', $file);
    }

    public function testSetTemplateRaiseExceptionIfTheExtensionOfTemplateIsNotPhtml()
    {
        $resolver = new TemplateResolver();
        $file     = $this->filesDir . 'development.html';
        $this->setExpectedException('InvalidArgumentException');
        $resolver->setTemplate('error/development', $file);
    }

    public function testResolveRaiseExceptionIfNonStandartTemplateNameSpecified()
    {
        $resolver = new TemplateResolver();
        $this->setExpectedException('UnexpectedValueException');
        $resolver->resolve('foo');
    }

    public function testResolveResolvesTemplate()
    {
        $resolver = new TemplateResolver();
        $file     = $this->filesDir . 'development.phtml';
        $resolver->setTemplate('error/development', $file);
        $this->assertSame($file, $resolver->resolve('error/development'));
    }
}
