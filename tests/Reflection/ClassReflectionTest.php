<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ClassReflectionTest extends TestCase
{

    public function testClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

    public function testClassException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestInterface');
    }

    public function testAbstractClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AbstractTestClass');
        $this->assertTrue($class->isAbstract());
    }

    public function testFinalClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\FinalTestClass');
        $this->assertTrue($class->isFinal());
    }

}