<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;

class MethodTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $method = new Generator\MethodGenerator('foo', 'protected');
        $method->setDesc('This is a method');
        $method->setDesc('This is a method, really');
        $method->setIndent('    ');
        $method->setAbstract(true);
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $method);
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $method->getDocblock());
        $this->assertEquals('foo', $method->getName());
        $this->assertEquals('This is a method, really', $method->getDesc());
        $this->assertEquals('    ', $method->getIndent());
        $this->assertEquals('protected', $method->getVisibility());
        $this->assertFalse($method->isStatic());
        $this->assertFalse($method->isInterface());
        $this->assertFalse($method->isFinal());
        $this->assertTrue($method->isAbstract());
    }

    public function testBody()
    {
        $method = new Generator\MethodGenerator('foo', 'protected');
        $method->setBody("echo 'Hello World';");
        $method->appendToBody("echo 'Foo!';");
        $this->assertContains("echo 'Hello World';", $method->getBody());
        $this->assertContains("echo 'Foo!';", $method->getBody());
    }

    public function testArguments()
    {
        $method = new Generator\MethodGenerator('foo', 'protected');
        $method->addParameter('foo', 'bar', 'string');
        $method->addParameters([
            [
                'name'  => 'baz',
                'value' => 123,
                'type'  => 'int'
            ]
        ]);
        $this->assertEquals('bar', $method->getArgument('foo')['value']);
        $this->assertEquals(123, $method->getParameter('baz')['value']);
        $this->assertEquals(2, count($method->getArguments()));
        $this->assertEquals(2, count($method->getParameters()));
    }

    public function testRender()
    {
        $method = new Generator\MethodGenerator('foo', 'protected');
        $method->addParameter('foo', 'bar', 'string');
        $method->addParameters([
            [
                'name'  => 'baz',
                'value' => 123,
                'type'  => 'int'
            ]
        ]);

        ob_start();
        $method->render();
        $result = ob_get_clean();
        $string = (string)$method;

        $this->assertContains('* @param string $foo', $result);
        $this->assertContains('* @param string $foo', $string);
        $this->assertContains('protected function foo($foo = bar, $baz = 123)', $result);
        $this->assertContains('protected function foo($foo = bar, $baz = 123)', $string);
    }

}