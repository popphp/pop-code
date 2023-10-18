<?php

namespace Pop\Code\Test;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ReflectionTest extends TestCase
{

    public function testCreateClass()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\TestClass');
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
        $code1 = function($var): string|null {
            echo $var;
        };

        $function1 = Reflection::createFunction($code1);
        $code2 = function($var): array|string|null {
            echo $var;
        };

        $function2 = Reflection::createFunction($code2);
        $this->assertInstanceOf('Pop\Code\Generator\FunctionGenerator', $function1);
        $this->assertInstanceOf('Pop\Code\Generator\FunctionGenerator', $function2);
    }

    public function testCreateMethod()
    {
        $class   = new \ReflectionClass('Pop\Code\Generator\ConstantGenerator');
        $methods = $class->getMethods();

        $method = Reflection::createMethod($methods[0]);
        $this->assertInstanceOf('Pop\Code\Generator\MethodGenerator', $method);
    }

    public function testCreateProperty()
    {
        $class      = new \ReflectionClass('Pop\Code\Generator\ConstantGenerator');
        $properties = $class->getProperties();

        $property = Reflection::createProperty($properties[0]);
        $this->assertInstanceOf('Pop\Code\Generator\PropertyGenerator', $property);
    }

}