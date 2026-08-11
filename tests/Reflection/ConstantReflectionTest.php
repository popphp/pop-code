<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ConstantReflectionTest extends TestCase
{

    public function testTypedConstantPreservesTypeAndVisibility()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $constant = $class->getConstant('LABEL');

        $this->assertEquals('protected', $constant->getVisibility());
        $this->assertTrue($constant->isTyped());
        $this->assertStringContainsString('protected const string LABEL', (string)$constant);
    }

    public function testPrivateConstantVisibilityIsPreserved()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $constant = $class->getConstant('FLAG');

        $this->assertEquals('private', $constant->getVisibility());
        $this->assertStringContainsString('private const bool FLAG = true;', (string)$constant);
    }

    public function testUntypedLegacyConstantIsNotMarkedTyped()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $constant = $class->getConstant('LEGACY');

        $this->assertFalse($constant->isTyped());
        $this->assertEquals('public', $constant->getVisibility());
    }

    public function testConstantAttributesAreDetected()
    {
        $class    = Reflection::createClass('Pop\Code\Test\TestAssets\AttributedTestClass');
        $constant = $class->getConstant('LIMIT');

        $this->assertTrue($constant->hasAttribute('TagAttribute'));
        $this->assertStringContainsString("#[TagAttribute('const')]", (string) $constant);
    }

}
