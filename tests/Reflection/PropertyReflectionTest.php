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
