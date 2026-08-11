<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class PropertyReflectionTest extends TestCase
{

    public function testTypedPropertyWithoutDocblockRendersCorrectTypeHint()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $property = $class->getProperty('count');

        $this->assertEquals('int', $property->getType());
        $this->assertStringContainsString('protected int $count = 0;', (string)$property);
    }

    public function testPropertyWithoutDefaultIsNotDropped()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');

        $this->assertTrue($class->hasProperty('noDefault'));
        $this->assertStringContainsString('string|null $noDefault = null;', (string)$class->getProperty('noDefault'));
    }

    public function testUnionTypedPropertyDefaultPreservesTheActualValueType()
    {
        // Previously ValueFormatter didn't understand union type strings and coerced this int
        // default to a quoted string literal ('1' instead of 1).
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $property = $class->getProperty('unionProp');
        $render   = (string) $property;

        $this->assertStringContainsString('$unionProp = 1;', $render);
        $this->assertStringNotContainsString("'1'", $render);
    }

    public function testIntersectionTypedPropertyRegeneratesAsValidPhp()
    {
        // Previously PropertyReflection's type resolution (via the shared
        // TypeNormalizer::resolveReflectionType()) correctly detected the intersection type, but
        // PropertyGenerator's own |null-widening for a no-default property didn't know about `&`
        // types and produced invalid PHP (Countable&Traversable|null with no parens).
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $property = $class->getProperty('intersectionProp');
        $render   = (string) $property;

        $this->assertStringContainsString('(Countable&Traversable)|null', $render);

        $tmpFile = sys_get_temp_dir() . '/pop-code-intersection-prop-refl-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\nclass Tmp {\n" . $render . "\n}\n");
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output));
    }

    public function testPromotedConstructorPropertyIsNotDuplicatedAsAProperty()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\PromotedPropertyTestClass');

        $this->assertFalse($class->hasProperty('x'));
        $this->assertTrue($class->hasProperty('noDefault'));
    }

    public function testReadonlyPropertyIsDetectedOnAnOrdinaryClass()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ReadonlyPropertyTestClass');
        $token    = $class->getProperty('token');
        $mutable  = $class->getProperty('mutable');

        $this->assertTrue($token->isReadonly());
        $this->assertStringContainsString('public readonly string $token;', (string) $token);
        $this->assertFalse($mutable->isReadonly());
    }

    public function testPromotedPropertiesOfAllVisibilitiesAreExcludedFromPropertyList()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\PromotedPropertyTestClass');

        $this->assertFalse($class->hasProperty('x'));
        $this->assertFalse($class->hasProperty('y'));
        $this->assertFalse($class->hasProperty('flag'));
        $this->assertTrue($class->hasProperty('noDefault'));
    }

    public function testPropertyAttributesAreDetected()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\AttributedTestClass');
        $property = $class->getProperty('label');

        $this->assertTrue($property->hasAttribute('TagAttribute'));
        $this->assertStringContainsString("#[TagAttribute('prop')]", (string) $property);
    }

    public function testForeignNamespaceMemberAttributeHasNoUseImport()
    {
        // Documented limitation: member-level attributes render a bare short name with no
        // auto-generated use import, since PropertyReflection has no access to the enclosing
        // class's namespace object. This test pins that as intentional, not silently wrong.
        // Reuses Task 7's AttributedTestClass/ForeignTagAttribute fixtures.
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\AttributedTestClass');
        $property = $class->getProperty('label');
        $render   = (string) $property;

        $this->assertStringContainsString('#[ForeignTagAttribute(', $render);
        // The class-level use IS present (from the class-level attribute) -- confirm the
        // member-level attribute did not additionally trigger anything beyond that single import.
        $useCount = 0;
        foreach ($class->getNamespace()->getUses() as $use => $as) {
            if ($use === 'Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute') {
                $useCount++;
            }
        }
        $this->assertEquals(1, $useCount);
    }

}
