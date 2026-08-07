<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class InterfaceReflectionTest extends TestCase
{

    use RoundTripExecutionTrait;

    public function testInterfaceException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestClass');
    }

    public function testInterfaceReflectsRealInterface()
    {
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestInterface');

        $this->assertInstanceOf('Pop\Code\Generator\InterfaceGenerator', $interface);
        $this->assertEquals('TestInterface', $interface->getName());
        $this->assertTrue($interface->hasConstant('FOO'));
        $this->assertTrue($interface->hasMethod('bar'));
        $this->assertTrue($interface->hasMethod('printSomething'));
    }

    public function testInterfaceExtendsIsDetected()
    {
        // Previously InterfaceReflection called getParentClass(), which is always false for an
        // interface -- extends is reported via getInterfaces() instead. This silently dropped
        // every interface's `extends` clause.
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestInterface');

        $this->assertEquals('ParentInterface', $interface->getParent());
        $this->assertStringContainsString('extends ParentInterface', (string) $interface);
    }

    public function testInterfaceMethodsDoNotRenderAbstractKeyword()
    {
        // Previously every interface method rendered with a literal `abstract` keyword (native
        // reflection reports isAbstract() = true for all interface methods, and that was applied
        // unconditionally) -- PHP forbids `abstract` inside an interface body outright.
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestInterface');
        $render    = (string) $interface;

        $this->assertStringNotContainsString('abstract', $render);
        $this->assertStringContainsString('public function bar($baz);', $render);
    }

    public function testInterfaceRegeneratesAsValidPhpThatLoads()
    {
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestInterface');
        $render    = (string) $interface;

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

}
