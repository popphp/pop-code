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

    public function testAttributedClassRegeneratesAsValidPhpThatLoads()
    {
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AttributedTestClass');
        $render = (string) $class;

        // (string) $class is a fragment (no opening tag, no autoloader) -- ClassGenerator::render()
        // never emits '<?php', that's added only by the file-level Generator class. Both are required
        // to actually execute the fragment in a subprocess rather than just checking php -l syntax.
        //
        // The rendered fragment leads with a namespace declaration, and PHP requires `namespace` to be
        // the very first statement in a file (only `declare()` may precede it) -- so the autoload
        // require must be inserted *after* the namespace line, not prepended before it.
        $autoload         = dirname(__DIR__, 2) . '/vendor/autoload.php';
        $requireStatement = 'require ' . var_export($autoload, true) . ';' . PHP_EOL;
        if (preg_match('/^(.*?namespace\s+[^;]+;\s*\n)/s', $render, $matches)) {
            $content = '<?php' . PHP_EOL . $matches[1] . $requireStatement . substr($render, strlen($matches[1]));
        } else {
            $content = '<?php' . PHP_EOL . $requireStatement . $render;
        }

        $tmpFile = sys_get_temp_dir() . '/pop-code-attributed-class-' . uniqid() . '.php';
        file_put_contents($tmpFile, $content);
        exec('php ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

}