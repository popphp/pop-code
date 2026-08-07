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

    public function testRootNamespaceAttributeIsPreservedWithLeadingBackslash()
    {
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\RootNamespaceAttributeTestClass');
        $render = (string) $class;

        // AttributeCollector must not reduce a genuinely root-namespace attribute (PHP's own
        // #[\AllowDynamicProperties], which has no backslash in ReflectionAttribute::getName())
        // down to its short name. If it did, the bare "#[AllowDynamicProperties]" emitted here
        // would silently resolve to the WRONG class once regenerated inside a namespaced
        // construct -- PHP resolves attribute classes lazily, so that produces no error at
        // load time, only when something actually asks for the attribute instance.
        $this->assertStringContainsString('#[\AllowDynamicProperties]', $render);
    }

    public function testRootNamespaceAttributeResolvesCorrectlyWhenRegeneratedIntoNamespace()
    {
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\RootNamespaceAttributeTestClass');
        $render = (string) $class;

        // Regenerate the class into a *different* namespace than the fixture's own, then have a
        // subprocess actually resolve the attribute instance. If AttributeCollector had reduced
        // #[\AllowDynamicProperties] to the bare short name, PHP would resolve it relative to
        // this new namespace (e.g. Pop\Code\Test\Reflection\Regenerated\AllowDynamicProperties),
        // which doesn't exist -- but since attribute resolution is lazy, that failure would only
        // surface here, at the point newInstance() is actually called.
        $autoload         = dirname(__DIR__, 2) . '/vendor/autoload.php';
        $requireStatement = 'require ' . var_export($autoload, true) . ';' . PHP_EOL;
        // Anchor to a line that actually *starts* with "namespace " (the real declaration), not
        // the "@namespace" docblock text that precedes it in the rendered fragment.
        if (preg_match('/^namespace\s+[^;]+;\s*\n/m', $render, $matches)) {
            $newNamespace   = 'Pop\\Code\\Test\\Reflection\\Regenerated';
            $namespaceLine  = 'namespace ' . $newNamespace . ';' . PHP_EOL;
            $renderRewritten = substr_replace($render, $namespaceLine, strpos($render, $matches[0]), strlen($matches[0]));
            $content        = '<?php' . PHP_EOL
                . substr($renderRewritten, 0, strpos($renderRewritten, $namespaceLine) + strlen($namespaceLine))
                . $requireStatement
                . substr($renderRewritten, strpos($renderRewritten, $namespaceLine) + strlen($namespaceLine))
                . PHP_EOL
                . '$refl = new \ReflectionClass(\\' . $newNamespace . '\RootNamespaceAttributeTestClass::class);'
                . 'exit($refl->getAttributes()[0]->newInstance() instanceof \AllowDynamicProperties ? 0 : 1);';
        } else {
            $this->fail('Rendered class did not contain an expected namespace declaration: ' . $render);
        }

        $tmpFile = sys_get_temp_dir() . '/pop-code-root-namespace-attribute-' . uniqid() . '.php';
        file_put_contents($tmpFile, $content);
        exec('php ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

}