<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ClassReflectionTest extends TestCase
{

    use RoundTripExecutionTrait;

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

    public function testSameNamespaceParentClassDoesNotGetARedundantUseImport()
    {
        // TestClass extends AbstractTestClass and implements TestInterface, both in the same
        // namespace (Pop\Code\Test\TestAssets) -- both resolve correctly by their bare short
        // names with no import, matching the same-namespace guard already applied to attributes.
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');

        if ($class->hasNamespace()) {
            $this->assertFalse($class->getNamespace()->hasUse('Pop\Code\Test\TestAssets\AbstractTestClass'));
            $this->assertFalse($class->getNamespace()->hasUse('Pop\Code\Test\TestAssets\TestInterface'));
        } else {
            $this->assertFalse($class->hasNamespace());
        }
    }

    public function testInheritedInterfaceIsNotHoistedOntoTheChildClass()
    {
        // TestClass extends AbstractTestClass, which implements TestInterface (which itself
        // extends ParentInterface). TestClass does not declare `implements` itself -- previously
        // getInterfaces()'s full transitive closure was rendered onto TestClass directly, as if
        // it had declared `implements ParentInterface, TestInterface` when it declared neither.
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');

        $this->assertFalse($class->hasInterfaces());
        $this->assertStringNotContainsString('implements', (string) $class);
    }

    public function testInheritedMethodsAndPropertiesAreNotHoistedOntoTheChildClass()
    {
        // AbstractTestClass declares abstract bar()/printSomething(), both overridden (redeclared)
        // by TestClass -- those two should still appear, since the override is a real declaration
        // on TestClass. Trait-provided members (traitProp/baz(), via `use TestTrait;`) should also
        // still appear -- PHP reports a trait member's declaring class as the class that uses the
        // trait, so declaring-class filtering correctly leaves those alone; it only excludes what's
        // purely inherited from the parent CLASS chain.
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');

        $this->assertTrue($class->hasMethod('bar'));
        $this->assertTrue($class->hasMethod('printSomething'));
        $this->assertTrue($class->hasMethod('baz'));
        $this->assertTrue($class->hasProperty('traitProp'));
        $this->assertTrue($class->hasProperty('myProp'));
    }

    public function testMethodDocblockDoesNotDuplicateParamFromSourceAndReflection()
    {
        // TestClass::bar($baz)'s source docblock is a bare "@param $baz" with no type -- combined
        // with MethodReflection's own addArgument() call for the same parameter, this used to
        // render two separate "@param $baz" lines for one parameter.
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\TestClass');
        $render = (string) $class->getMethod('bar');

        $this->assertEquals(1, substr_count($render, '@param'));
    }

    public function testSameNamespaceAttributeDoesNotGetARedundantUseImport()
    {
        // TagAttribute lives in the same namespace as AttributedTestClass itself
        // (Pop\Code\Test\TestAssets) -- it resolves correctly by its bare short name with no
        // import, so ClassReflection must not add one (the class already carries a use import
        // for the foreign-namespace ForeignTagAttribute, so the namespace object is non-empty
        // either way -- this asserts specifically that TagAttribute isn't also imported).
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\AttributedTestClass');

        $this->assertTrue($class->hasNamespace());
        $this->assertFalse($class->getNamespace()->hasUse('Pop\Code\Test\TestAssets\TagAttribute'));
    }

    public function testCollidingShortNamesFallBackToFullyQualifiedReferenceInsteadOfUseCollision()
    {
        // CollidingAttributesTestClass carries two attributes both short-named
        // ForeignTagAttribute, from two different foreign namespaces. Without collision handling,
        // regenerating this would emit two `use` statements for different classes sharing one
        // short name -- a PHP fatal error ("Cannot use X as Y because the name is already in
        // use"), not just invalid output.
        $class  = Reflection\ClassReflection::parse('Pop\Code\Test\TestAssets\CollidingAttributesTestClass');
        $render = (string) $class;

        $this->assertTrue($class->hasAttribute('ForeignTagAttribute'));
        $this->assertStringContainsString('\Pop\Code\Test\TestAssets\AttrsB\ForeignTagAttribute', $render);

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
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

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
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