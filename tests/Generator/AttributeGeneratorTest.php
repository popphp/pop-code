<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class AttributeGeneratorTest extends TestCase
{

    public function testBareAttributeRenders()
    {
        $attribute = new Generator\AttributeGenerator('Route');
        $this->assertEquals('Route', $attribute->getName());
        $this->assertFalse($attribute->hasArguments());
        $this->assertEquals('#[Route]', (string) $attribute);
    }

    public function testPositionalArgumentRenders()
    {
        $attribute = new Generator\AttributeGenerator('Tag');
        $attribute->addArgument('a');
        $this->assertTrue($attribute->hasArguments());
        $this->assertEquals("#[Tag('a')]", (string) $attribute);
    }

    public function testNamedArgumentRenders()
    {
        $attribute = new Generator\AttributeGenerator('Tag');
        $attribute->addArgument('b', 'name');
        $attribute->addArgument(5, 'priority');
        $this->assertEquals("#[Tag(name: 'b', priority: 5)]", (string) $attribute);
    }

    public function testMixedPositionalAndNamedArgumentsRender()
    {
        $attribute = new Generator\AttributeGenerator('Tag');
        $attribute->addArgument('a');
        $attribute->addArgument(5, 'priority');
        $this->assertEquals("#[Tag('a', priority: 5)]", (string) $attribute);
    }

    public function testLiteralArgumentRendersRaw()
    {
        $attribute = new Generator\AttributeGenerator('Route');
        $attribute->addArgument(new Generator\Literal('HttpMethod::GET'), 'method');
        $this->assertEquals('#[Route(method: HttpMethod::GET)]', (string) $attribute);
    }

    public function testRenderNeverIndents()
    {
        $attribute = new Generator\AttributeGenerator('Route');
        $attribute->setIndent(8);
        $this->assertEquals('#[Route]', $attribute->render());
    }

    public function testArrayArgumentRendersOnASingleLine()
    {
        // Attribute arguments always render inline -- even a top-level #[...] is
        // conventionally one physical line, and an array value would otherwise force a
        // multi-line bracket literal into the middle of it (or worse, into a parameter list).
        $attribute = new Generator\AttributeGenerator('Route');
        $attribute->addArgument(['GET', 'POST'], 'methods');
        $render = (string) $attribute;

        $this->assertEquals("#[Route(methods: ['GET', 'POST'])]", $render);
        $this->assertStringNotContainsString(PHP_EOL, $render);
    }

}
