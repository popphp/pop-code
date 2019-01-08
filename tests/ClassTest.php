<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class ClassTest extends TestCase
{

    public function testConstructor()
    {
        $class = new Generator\ClassGenerator('MyClass', 'ParentClass', 'MyInterface', true);
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
        $this->assertEquals('MyClass', $class->getName());
        $this->assertEquals('ParentClass', $class->getParent());
        $this->assertEquals('MyInterface', $class->getInterface());
        $this->assertTrue($class->isAbstract());
    }

    public function testIndent()
    {
        $class = new Generator\ClassGenerator('MyClass');
        $class->setIndent('    ');
        $this->assertEquals('    ', $class->getIndent());
    }

    public function testNamespace()
    {
        $class = new Generator\ClassGenerator('MyClass');
        $class->setNamespace(new Generator\NamespaceGenerator('mynamespace'));
        $this->assertInstanceOf('Pop\Code\Generator\NamespaceGenerator', $class->getNamespace());
    }

    public function testDocblock()
    {
        $class = new Generator\ClassGenerator('MyClass');
        $class->setDocblock(new Generator\DocblockGenerator('mydocblock'));
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $class->getDocblock());
    }

    public function testProperties()
    {
        $class = new Generator\ClassGenerator('MyClass');
        $class->addProperty(new Generator\PropertyGenerator('foo', 'string'));
        $this->assertInstanceOf('Pop\Code\Generator\PropertyGenerator', $class->getProperty('foo'));
        $this->assertEquals(1, count($class->getProperties()));
        $class->removeProperty('foo');
        $this->assertEquals(0, count($class->getProperties()));
    }

    public function testMethods()
    {
        $class = new Generator\ClassGenerator('MyClass');
        $class->addMethod(new Generator\MethodGenerator('foo'));
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $class->getMethod('foo'));
        $this->assertEquals(1, count($class->getMethods()));
        $class->removeMethod('foo');
        $this->assertEquals(0, count($class->getMethods()));
    }

    public function testRender()
    {
        $class = new Generator\ClassGenerator('MyClass', 'ParentClass', 'MyInterface', true);
        $class->addProperty(new Generator\PropertyGenerator('foo', 'string'));
        $class->addMethod(new Generator\MethodGenerator('bar'));
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
        $this->assertEquals('MyClass', $class->getName());
        $this->assertEquals('ParentClass', $class->getParent());
        $this->assertEquals('MyInterface', $class->getInterface());
        $this->assertTrue($class->isAbstract());

        ob_start();
        $class->render();
        $result = ob_get_clean();
        $string = (string)$class;

        $this->assertContains('abstract class MyClass extends ParentClass implements MyInterface', $result);
        $this->assertContains('abstract class MyClass extends ParentClass implements MyInterface', $string);
    }

}