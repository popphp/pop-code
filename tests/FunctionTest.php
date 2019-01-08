<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class FunctionTest extends TestCase
{

    public function testConstructor()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->setDesc('This is a function');
        $function->setDesc('This is a function, really');
        $function->setIndent('    ');
        $function->setClosure(true);
        $this->assertInstanceOf('Pop\Code\Generator\FunctionGenerator', $function);
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $function->getDocblock());
        $this->assertEquals('foo', $function->getName());
        $this->assertEquals('This is a function, really', $function->getDesc());
        $this->assertEquals('    ', $function->getIndent());
        $this->assertTrue($function->isClosure());
    }

    public function testParse()
    {
        $function = new Generator\FunctionGenerator('foo', function($foo, $bar){});
        $this->assertContains('foo', $function->getArgumentNames());
        $this->assertContains('bar', $function->getParameterNames());

    }

    public function testBody()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->setBody("echo 'Hello World';");
        $function->appendToBody("echo 'Foo!';");
        $this->assertContains("echo 'Hello World';", $function->getBody());
        $this->assertContains("echo 'Foo!';", $function->getBody());
    }

    public function testArguments()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->addParameter('foo', 'bar', 'string');
        $function->addParameters([
            [
                'name'  => 'baz',
                'value' => 123,
                'type'  => 'int'
            ]
        ]);
        $this->assertEquals('bar', $function->getArgument('foo')['value']);
        $this->assertEquals(123, $function->getParameter('baz')['value']);
        $this->assertEquals(2, count($function->getArguments()));
        $this->assertEquals(2, count($function->getParameters()));
        $this->assertContains('baz', $function->getArgumentNames());
        $this->assertContains('foo', $function->getParameterNames());
    }

    public function testRender()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->setDocblock(new Generator\DocblockGenerator('This is a docblock'));
        $function->addParameter('foo', 'bar', 'string');
        $function->addParameters([
            [
                'name'  => 'baz',
                'value' => 123,
                'type'  => 'int'
            ]
        ]);

        ob_start();
        $function->render();
        $result = ob_get_clean();
        $string = (string)$function;

        $this->assertContains('* @param string $foo', $result);
        $this->assertContains('* @param string $foo', $string);
        $this->assertContains('function foo($foo = bar, $baz = 123)', $result);
        $this->assertContains('function foo($foo = bar, $baz = 123)', $string);
    }

    public function testRenderClosure()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->setClosure(true);
        $function->setDocblock(new Generator\DocblockGenerator('This is a docblock'));
        $function->addParameter('foo', 'bar', 'string');
        $function->addParameters([
            [
                'name'  => 'baz',
                'value' => 123,
                'type'  => 'int'
            ]
        ]);

        ob_start();
        $function->render();
        $result = ob_get_clean();
        $string = (string)$function;

        $this->assertContains('* @param string $foo', $result);
        $this->assertContains('* @param string $foo', $string);
        $this->assertContains('$foo = function($foo = bar, $baz = 123)', $result);
        $this->assertContains('$foo = function($foo = bar, $baz = 123)', $string);
    }

}