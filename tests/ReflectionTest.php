<?php

namespace Pop\Code\Test;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ReflectionTest extends TestCase
{

    public function testCreateClass()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\Test');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

    public function testCreateTrait()
    {
        $trait = Reflection::createTrait('Pop\Code\Test\TestAssets\TestTrait');
        $this->assertInstanceOf('Pop\Code\Generator\TraitGenerator', $trait);
    }

    public function testCreateInterface()
    {
        $interface = Reflection::createInterface('Pop\Code\Test\TestAssets\TestInterface');
        $this->assertInstanceOf('Pop\Code\Generator\InterfaceGenerator', $interface);
    }

    public function testCreateNamespace()
    {
        $code = <<<CODE
namespace Foo;

use Bar;
use Baz;
CODE;

        $namespace = Reflection::createNamespace($code);
        $this->assertInstanceOf('Pop\Code\Generator\NamespaceGenerator', $namespace);
    }

    public function testCreateDocblock()
    {
        $code = <<<CODE
/**
 * Test desc
 *
 * @param string \$str
 * @returns array
 */
CODE;

        $docblock = Reflection::createDocblock($code);
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $docblock);
    }

    public function testCreateFunction()
    {
        $code = function($var) {
            echo $var;
        };

        $function = Reflection::createFunction($code);
        $this->assertInstanceOf('Pop\Code\Generator\FunctionGenerator', $function);
    }

    public function testCreateMethod()
    {
        $class   = new \ReflectionClass('Pop\Code\Test\TestAssets\Test');
        $methods = $class->getMethods();

        $method = Reflection::createMethod($methods[0]);
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $method);
    }

    public function testCreateProperty()
    {
        $class      = new \ReflectionClass('Pop\Code\Test\TestAssets\Test');
        $properties = $class->getProperties();

        $property = Reflection::createProperty($properties[0]);
        $this->assertInstanceOf('Pop\Code\Generator\PropertyGenerator', $property);
    }

}