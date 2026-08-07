<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class TraitReflectionTest extends TestCase
{

    use RoundTripExecutionTrait;

    public function testException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $trait = Reflection\TraitReflection::parse('Pop\Code\Test\TestAssets\TestClass');
    }

    public function testTraitReflectsRealTrait()
    {
        $trait = Reflection\TraitReflection::parse('Pop\Code\Test\TestAssets\TestTrait');

        $this->assertInstanceOf('Pop\Code\Generator\TraitGenerator', $trait);
        $this->assertEquals('TestTrait', $trait->getName());
        $this->assertTrue($trait->hasProperty('traitProp'));
        $this->assertTrue($trait->hasMethod('baz'));
    }

    public function testTraitUseStatementsAreDetected()
    {
        $trait  = Reflection\TraitReflection::parse('Pop\Code\Test\TestAssets\TestTrait');
        $render = (string) $trait;

        $this->assertTrue($trait->hasUse('AnotherTrait'));
        $this->assertTrue($trait->hasUse('OneMoreTrait'));
        $this->assertStringContainsString('use AnotherTrait;', $render);
        $this->assertStringContainsString('use OneMoreTrait;', $render);
    }

    public function testTraitRegeneratesAsValidPhpThatLoads()
    {
        $trait  = Reflection\TraitReflection::parse('Pop\Code\Test\TestAssets\TestTrait');
        $render = (string) $trait;

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

}
