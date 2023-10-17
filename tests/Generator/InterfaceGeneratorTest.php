<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class InterfaceGeneratorTest extends TestCase
{

    public function testParent()
    {
        $interface = new Generator\InterfaceGenerator('FooInterface', 'AbstractFoo');
        $this->assertTrue($interface->hasParent());
        $this->assertEquals('AbstractFoo', $interface->getParent());
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

}