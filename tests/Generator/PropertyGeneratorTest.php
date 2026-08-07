<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class PropertyGeneratorTest extends TestCase
{

    public function testTypeAndValue()
    {
        $property = new Generator\PropertyGenerator('foo', 'string', 'foo_value');
        $property->setDesc('Desc');
        $this->assertTrue($property->hasDesc());
        $this->assertTrue($property->hasName());
        $this->assertEquals('Desc', $property->getDesc());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('foo_value', $property->getValue());
        $this->assertTrue($property->hasValue());
        $this->assertTrue($property->hasType());
    }

    public function testRender()
    {
        $property = new Generator\PropertyGenerator('foo', 'string', 'foo_value');
        $this->assertStringContainsString("public string \$foo = 'foo_value'", (string)$property);
    }

    public function testRenderInt()
    {
        $property = new Generator\PropertyGenerator('foo', 'int', 1);
        $this->assertStringContainsString("public int \$foo = 1", (string)$property);
    }

    public function testRenderBoolean()
    {
        $property = new Generator\PropertyGenerator('foo', 'bool', true);
        $this->assertStringContainsString("public bool \$foo = true", (string)$property);
    }

    public function testRenderArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', [1, 2, 3]);
        $this->assertStringContainsString("public array \$foo = [", (string)$property);
        $this->assertStringContainsString("];", (string)$property);
    }

    public function testRenderAssocArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', ['x' => 1, 'y' => 2, 'z' => 3]);
        $this->assertStringContainsString("public array \$foo = [", (string)$property);
        $this->assertStringContainsString("];", (string)$property);
    }

    public function testReadonlyRendersWithoutDefaultValue()
    {
        $property = new Generator\PropertyGenerator('foo', 'string');
        $property->setAsReadonly(true);
        $render = (string) $property;

        $this->assertTrue($property->isReadonly());
        $this->assertStringContainsString('public readonly string $foo;', $render);
        $this->assertStringNotContainsString('= null', $render);
    }

    public function testReadonlyIsMutuallyExclusiveWithStatic()
    {
        $property = new Generator\PropertyGenerator('foo', 'string');
        $property->setAsStatic(true);
        $property->setAsReadonly(true);
        $this->assertFalse($property->isStatic());
        $this->assertTrue($property->isReadonly());

        $property->setAsStatic(true);
        $this->assertFalse($property->isReadonly());
        $this->assertTrue($property->isStatic());
    }

    public function testReadonlyWithoutTypeThrowsException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $property = new Generator\PropertyGenerator('foo');
        $property->setAsReadonly(true);
        $property->render();
    }

}