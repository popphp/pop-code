<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ClassReflectionTest extends TestCase
{

    public function testClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

    public function testClassException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestInterface');
    }

    public function testAbstractClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AbstractTestClass');
        $this->assertTrue($class->isAbstract());
    }

    public function testFinalClass()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\FinalTestClass');
        $this->assertTrue($class->isFinal());
    }

    public function testEnumThrowsReflectionException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestEnum');
    }

    public function testReadonlyClassSuppressesRedundantPropertyReadonly()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\ReadonlyClassTestClass');
        $render = (string) $class;

        $this->assertTrue($class->isReadonly());
        $this->assertStringContainsString('readonly class ReadonlyClassTestClass', $render);
        // Every property is implicitly readonly via the class keyword — no per-property "readonly" needed.
        $this->assertStringNotContainsString('public readonly int $id', $render);
        $this->assertStringNotContainsString('protected readonly string $label', $render);

        $tmpFile = sys_get_temp_dir() . '/pop-code-readonly-class-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\n" . $render);
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output));
    }

    public function testCompositeModifiersRoundTripAsValidPhp()
    {
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\PromotedPropertyTestClass');
        $render = (string) $class;

        $tmpFile = sys_get_temp_dir() . '/pop-code-composite-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\n" . $render);
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $render);
    }

    public function testClassLevelAttributesAreDetectedIncludingRepeated()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AttributedTestClass');

        $this->assertTrue($class->hasAttribute('TagAttribute'));
        $this->assertEquals(2, count($class->getAttributesByName('TagAttribute')));
    }

    public function testForeignNamespaceClassAttributeGetsAUseImport()
    {
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AttributedTestClass');
        $render = (string) $class;

        $this->assertTrue($class->hasNamespace());
        $this->assertTrue($class->getNamespace()->hasUse('Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute'));
        $this->assertStringContainsString('#[ForeignTagAttribute(', $render);
    }

    public function testConstantAndMethodAndParameterAttributesAreDetected()
    {
        $class    = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AttributedTestClass');
        $constant = $class->getConstant('LIMIT');
        $method   = $class->getMethod('greet');

        $this->assertStringContainsString('#[TagAttribute(', (string) $constant);
        $this->assertStringContainsString('#[TagAttribute(', (string) $method);
        $this->assertStringContainsString("#[TagAttribute('param')] string \$name", (string) $method);
    }

}