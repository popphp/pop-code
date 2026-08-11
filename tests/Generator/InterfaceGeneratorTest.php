<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class InterfaceGeneratorTest extends TestCase
{

    public function testParent()
    {
        $interface = new Generator\InterfaceGenerator('FooInterface', 'AbstractFoo');
        $this->assertTrue($interface->hasParents());
        $this->assertTrue($interface->hasParent('AbstractFoo'));
        $this->assertEquals(['AbstractFoo'], $interface->getParents());
    }

    public function testMultipleParentsViaCommaSeparatedString()
    {
        // A PHP interface can extend more than one interface at once, unlike a class.
        $interface = new Generator\InterfaceGenerator('FooInterface', 'A, B');
        $this->assertTrue($interface->hasParent('A'));
        $this->assertTrue($interface->hasParent('B'));
        $this->assertEquals(['A', 'B'], $interface->getParents());
        $this->assertStringContainsString('interface FooInterface extends A, B', (string) $interface);
    }

    public function testMultipleParentsViaArray()
    {
        $interface = new Generator\InterfaceGenerator('FooInterface', ['A', 'B']);
        $this->assertEquals(['A', 'B'], $interface->getParents());
    }

    public function testAddAndRemoveParent()
    {
        $interface = new Generator\InterfaceGenerator('FooInterface');
        $interface->addParent('A');
        $interface->addParents(['B', 'C']);
        $this->assertEquals(['A', 'B', 'C'], $interface->getParents());

        $interface->removeParent('B');
        $this->assertEquals(['A', 'C'], array_values($interface->getParents()));
        $this->assertFalse($interface->hasParent('B'));
    }

    public function testRender()
    {
        $interface = new Generator\InterfaceGenerator('FooInterface', 'AbstractFoo');

        $interface->addConstants([
            new Generator\ConstantGenerator('SOME_CONSTANT', 'string', 'STRING'),
            new Generator\ConstantGenerator('SOME_OTHER_CONSTANT', 'string', 'STRING')
        ]);

        $interface->addMethods([
            new Generator\MethodGenerator('foo'),
            new Generator\MethodGenerator('bar')
        ]);

        $render = (string)$interface;

        $this->assertStringContainsString('interface FooInterface extends AbstractFoo', $render);
        $this->assertStringContainsString("const SOME_CONSTANT = 'STRING';", $render);
        $this->assertStringContainsString("const SOME_OTHER_CONSTANT = 'STRING';", $render);
        $this->assertStringContainsString('public function foo();', $render);
        $this->assertStringContainsString('public function bar();', $render);
    }

    public function testAttributesRenderBeforeInterfaceKeywordWithNoIndent()
    {
        $interface = new Generator\InterfaceGenerator('Foo');
        $interface->addAttribute(new Generator\AttributeGenerator('Contract'));
        $render = (string) $interface;

        $this->assertStringContainsString("#[Contract]\ninterface Foo", $render);
    }

}