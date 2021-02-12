<?php

namespace Pop\Code\Test;

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
        $this->assertStringContainsString("public \$foo = 'foo_value'", (string)$property);
    }

    public function testRenderInt()
    {
        $property = new Generator\PropertyGenerator('foo', 'int', 1);
        $this->assertStringContainsString("public \$foo = 1", (string)$property);
    }

    public function testRenderBoolean()
    {
        $property = new Generator\PropertyGenerator('foo', 'boolean', true);
        $this->assertStringContainsString("public \$foo = true", (string)$property);
    }

    public function testRenderArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', [1, 2, 3]);
        $this->assertStringContainsString("public \$foo = [", (string)$property);
        $this->assertStringContainsString("];", (string)$property);
    }

    public function testRenderAssocArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', ['x' => 1, 'y' => 2, 'z' => 3]);
        $this->assertStringContainsString("public \$foo = [", (string)$property);
        $this->assertStringContainsString("];", (string)$property);
    }

}