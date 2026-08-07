<?php

namespace Pop\Code\Test\Reflection\Support;

use Pop\Code\Reflection\Support\AttributeCollector;
use PHPUnit\Framework\TestCase;

#[\Pop\Code\Test\TestAssets\TagAttribute('a')]
#[\Pop\Code\Test\TestAssets\TagAttribute(name: 'b', priority: 5)]
class AttributeCollectorTestFixture
{
}

class AttributeCollectorTest extends TestCase
{

    public function testBuildsPositionalArgument()
    {
        $reflection = new \ReflectionClass(AttributeCollectorTestFixture::class);
        $attributes = $reflection->getAttributes();

        $attribute = AttributeCollector::build($attributes[0]);

        $this->assertEquals('TagAttribute', $attribute->getName());
        $this->assertStringContainsString("TagAttribute('a')", (string) $attribute);
    }

    public function testBuildsNamedArguments()
    {
        $reflection = new \ReflectionClass(AttributeCollectorTestFixture::class);
        $attributes = $reflection->getAttributes();

        $attribute = AttributeCollector::build($attributes[1]);

        $this->assertStringContainsString("name: 'b', priority: 5", (string) $attribute);
    }

}
