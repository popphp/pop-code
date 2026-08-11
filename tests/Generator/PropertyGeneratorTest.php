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

    public function testAttributesRenderIndentedBeforeProperty()
    {
        $property = new Generator\PropertyGenerator('foo', 'string');
        $property->addAttribute(new Generator\AttributeGenerator('Column'));
        $render = (string) $property;

        $this->assertStringContainsString("    #[Column]\n    public string", $render);
    }

    public function testLiteralValueRendersVerbatim()
    {
        // Previously threw "Cannot format an object value of type ...Literal" -- Literal
        // unwrapping only happened in FunctionTrait and AttributeGenerator, not here.
        $property = new Generator\PropertyGenerator('baz', 'string', new Generator\Literal('self::FOO'));
        $this->assertStringContainsString('public string $baz = self::FOO;', (string) $property);
    }

    public function testIntersectionTypedPropertyWithNoValueWrapsInParensBeforeNull()
    {
        // Previously an intersection type got `|null` appended directly (`Countable&Traversable|null`),
        // which is invalid PHP -- DNF syntax requires parens around the intersection part
        // (`(Countable&Traversable)|null`) when combined with a union member.
        $property = new Generator\PropertyGenerator('inter', 'Countable&Traversable');
        $render   = (string) $property;

        $this->assertStringContainsString('(Countable&Traversable)|null $inter = null;', $render);

        $tmpFile = sys_get_temp_dir() . '/pop-code-intersection-prop-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\nclass Tmp {\n" . $render . "\n}\n");
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output));
    }

    public function testUnionTypedPropertyDefaultPreservesTheActualValueType()
    {
        // Previously ValueFormatter didn't understand union type strings, so an int|string
        // property defaulting to an actual int got its default silently coerced to a quoted
        // string literal.
        $property = new Generator\PropertyGenerator('uni', 'int|string', 1);
        $this->assertStringContainsString('$uni = 1;', (string) $property);
        $this->assertStringNotContainsString("'1'", (string) $property);
    }

}