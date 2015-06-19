<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;

class InterfaceTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface', 'ParentInterface');
        $this->assertInstanceOf('Pop\Code\Generator\InterfaceGenerator', $interface);
        $this->assertEquals('MyInterface', $interface->getName());
        $this->assertEquals('ParentInterface', $interface->getParent());
    }

    public function testIndent()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface');
        $interface->setIndent('    ');
        $this->assertEquals('    ', $interface->getIndent());
    }

    public function testNamespace()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface');
        $interface->setNamespace(new Generator\NamespaceGenerator('mynamespace'));
        $this->assertInstanceOf('Pop\Code\Generator\NamespaceGenerator', $interface->getNamespace());
    }

    public function testDocblock()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface');
        $interface->setDocblock(new Generator\DocblockGenerator('mydocblock'));
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $interface->getDocblock());
    }

    public function testMethods()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface');
        $interface->addMethod(new Generator\MethodGenerator('foo'));
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $interface->getMethod('foo'));
        $interface->removeMethod('foo');
        $this->assertNull($interface->getMethod('foo'));
    }

    public function testRender()
    {
        $interface = new Generator\InterfaceGenerator('MyInterface', 'ParentInterface');
        $interface->addMethod(new Generator\MethodGenerator('foo'));
        $this->assertInstanceOf('Pop\Code\Generator\InterfaceGenerator', $interface);
        $this->assertEquals('MyInterface', $interface->getName());
        $this->assertEquals('ParentInterface', $interface->getParent());

        ob_start();
        $interface->render();
        $result = ob_get_clean();
        $string = (string)$interface;

        $this->assertContains('interface MyInterface extends ParentInterface', $result);
        $this->assertContains('interface MyInterface extends ParentInterface', $string);
    }

}