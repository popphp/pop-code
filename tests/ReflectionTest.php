<?php

namespace Pop\Code\Test;

use Pop\Code;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $code = new Code\Reflection('Pop\Code\Test\TestAsset\TestClass');
        $this->assertInstanceOf('Pop\Code\Reflection', $code);
        $this->assertInstanceOf('Pop\Code\Generator', $code->generator());
        $this->assertEquals('Pop\Code\Test\TestAsset\TestClass', $code->getClassString());
        $this->assertTrue($code->isClassString());
        $this->assertFalse($code->isObjectInstance());
    }

    public function testObjectInstance()
    {
        $test = new TestAsset\TestClass('bar');
        $code = new Code\Reflection($test);
        $this->assertInstanceOf('Pop\Code\Reflection', $code);
        $this->assertInstanceOf('Pop\Code\Test\TestAsset\TestClass', $code->getObjectInstance());
        $this->assertEquals('Pop\Code\Test\TestAsset\TestClass', $code->getClassString());
        $this->assertFalse($code->isClassString());
        $this->assertTrue($code->isObjectInstance());
    }

    public function testAbstract()
    {
        $code = new Code\Reflection('Pop\Code\Test\TestAsset\TestAbstract');
        $this->assertTrue($code->isAbstract());
    }

}