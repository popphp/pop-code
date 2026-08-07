<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class ConstantGeneratorTest extends TestCase
{

    public function testTypeAndValue()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'string', 'FOO_VALUE');
        $this->assertEquals('string', $constant->getType());
        $this->assertEquals('FOO_VALUE', $constant->getValue());
        $this->assertTrue($constant->hasValue());
    }

    public function testRender()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'array');
        $this->assertStringContainsString('const FOO = []', (string)$constant);
    }

    public function testRenderInt()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'int', 1);
        $this->assertStringContainsString('const FOO = 1', (string)$constant);
    }

    public function testRenderBoolean()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'bool', true);
        $this->assertStringContainsString('const FOO = true', (string)$constant);
    }

    public function testRenderArray()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'array', [1, 2, 3]);
        $this->assertStringContainsString('const FOO = [', (string)$constant);
        $this->assertStringContainsString('];', (string)$constant);
    }

    public function testRenderAssocArray()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'array', ['x' => 1, 'y' => 2, 'z' => 3]);
        $this->assertStringContainsString('const FOO = [', (string)$constant);
        $this->assertStringContainsString('];', (string)$constant);
    }

    public function testVisibilityRenders()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'string', 'bar');
        $constant->setAsPrivate();
        $this->assertStringContainsString('private const FOO', (string)$constant);
    }

    public function testDefaultVisibilityIsPublic()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'string', 'bar');
        $this->assertStringContainsString('public const FOO', (string)$constant);
    }

    public function testTypedSignatureIsOptIn()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'int', 10);
        $this->assertFalse($constant->isTyped());
        $this->assertStringNotContainsString('const int FOO', (string)$constant);

        $constant->setTyped(true);
        $this->assertTrue($constant->isTyped());
        $this->assertStringContainsString('const int FOO', (string)$constant);
    }

    public function testAttributesRenderIndentedBeforeConstant()
    {
        $constant = new Generator\ConstantGenerator('FOO', 'string', 'bar');
        $constant->addAttribute(new Generator\AttributeGenerator('Deprecated'));
        $render = (string) $constant;

        $this->assertStringContainsString("    #[Deprecated]\n    public const", $render);
    }

    public function testLiteralValueRendersVerbatim()
    {
        // Previously threw "Cannot format an object value of type ...Literal" -- Literal
        // unwrapping only happened in FunctionTrait and AttributeGenerator, not here.
        $constant = new Generator\ConstantGenerator('BAZ', 'string', new Generator\Literal('self::FOO'));
        $this->assertStringContainsString('public const BAZ = self::FOO;', (string) $constant);
    }

}