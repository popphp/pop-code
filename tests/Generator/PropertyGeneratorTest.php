<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class PropertyGeneratorTest extends TestCase
{

    public function testTypeAndValue()
    {
        $property = new Generator\PropertyGenerator('foo', 'string', 'foo_value');
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('foo_value', $property->getValue());
        $this->assertTrue($property->hasValue());
        $this->assertTrue($property->hasType());
    }

    public function testRender()
    {
        $property = new Generator\PropertyGenerator('foo', 'string', 'foo_value');
        $this->assertContains("public \$foo = 'foo_value'", (string)$property);
    }

    public function testRenderInt()
    {
        $property = new Generator\PropertyGenerator('foo', 'int', 1);
        $this->assertContains("public \$foo = 1", (string)$property);
    }

    public function testRenderBoolean()
    {
        $property = new Generator\PropertyGenerator('foo', 'boolean', true);
        $this->assertContains("public \$foo = true", (string)$property);
    }

    public function testRenderArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', [1, 2, 3]);
        $this->assertContains("public \$foo = [", (string)$property);
        $this->assertContains("];", (string)$property);
    }

    public function testRenderAssocArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', ['x' => 1, 'y' => 2, 'z' => 3]);
        $this->assertContains("public \$foo = [", (string)$property);
        $this->assertContains("];", (string)$property);
    }

}