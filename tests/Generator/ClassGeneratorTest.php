<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class ClassGeneratorTest extends TestCase
{

    public function testConstructor()
    {
        $class = new Generator\ClassGenerator('Foo', 'AbstractFoo', 'FooInterface');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
        $this->assertTrue($class->hasParent());
        $this->assertEquals('AbstractFoo', $class->getParent());
        $this->assertTrue($class->hasInterfaces());
    }

    public function testIndent()
    {
        $class = new Generator\ClassGenerator('Foo');
        $class->setIndent(8);
        $this->assertTrue($class->hasIndent());
        $this->assertEquals(8, $class->getIndent());
    }

    public function testOutput()
    {
        $class = new Generator\ClassGenerator('Foo');
        $class->addProperty(new Generator\PropertyGenerator('var'));
        $render = $class->render();

        $this->assertTrue($class->hasOutput());
        $this->assertTrue($class->isRendered());
        $this->assertEquals($render, $class->getOutput());
    }

    public function testConstants()
    {
        $class = new Generator\ClassGenerator('Foo');

        $class->addConstants([
            new Generator\ConstantGenerator('SOME_CONSTANT', 'string', 'STRING'),
            new Generator\ConstantGenerator('SOME_OTHER_CONSTANT', 'string', 'STRING')
        ]);

        $this->assertTrue($class->hasConstants());
        $this->assertTrue($class->hasConstant('SOME_CONSTANT'));
        $this->assertEquals(2, count($class->getConstants()));
        $this->assertInstanceOf('Pop\Code\Generator\ConstantGenerator', $class->getConstant('SOME_CONSTANT'));

        $class->removeConstant('SOME_CONSTANT');
        $this->assertFalse($class->hasConstant('SOME_CONSTANT'));
    }

    public function testMethods()
    {
        $class = new Generator\ClassGenerator('Foo');

        $class->addMethods([
            new Generator\MethodGenerator('foo'),
            new Generator\MethodGenerator('bar')
        ]);

        $this->assertTrue($class->hasMethods());
        $this->assertTrue($class->hasMethod('foo'));
        $this->assertEquals(2, count($class->getMethods()));
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $class->getMethod('foo'));

        $class->removeMethod('foo');
        $this->assertFalse($class->hasMethod('foo'));
    }

    public function testInterfaces1()
    {
        $class = new Generator\ClassGenerator('Foo');

        $class->addInterface('FooInterface');
        $this->assertTrue($class->hasInterface('FooInterface'));
        $this->assertEquals(1, count($class->getInterfaces()));
        $class->removeInterface('FooInterface');
        $this->assertFalse($class->hasInterface('FooInterface'));
    }

    public function testInterfaces2()
    {
        $class = new Generator\ClassGenerator('Foo', null, ['FooInterface', 'OtherInterface']);
        $this->assertTrue($class->hasInterface('FooInterface'));
        $this->assertTrue($class->hasInterface('OtherInterface'));
    }

    public function testInterfaces3()
    {
        $class = new Generator\ClassGenerator('Foo', null, 'FooInterface,OtherInterface');
        $this->assertTrue($class->hasInterface('FooInterface'));
        $this->assertTrue($class->hasInterface('OtherInterface'));
    }

    public function testFinal()
    {
        $class = new Generator\ClassGenerator('Foo');
        $class->setAsFinal(true);
        $this->assertContains('final class Foo', $class->render());
    }

}