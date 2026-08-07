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

}
