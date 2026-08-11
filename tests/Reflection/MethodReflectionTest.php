<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class MethodReflectionTest extends TestCase
{

    public function testArgumentDefaultsRoundTripCorrectly()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('greet');
        $render = (string) $method;

        $this->assertStringContainsString("string \$name = 'world'", $render);
        $this->assertStringContainsString('bool $loud = false', $render);
        $this->assertStringContainsString('string|null $suffix = null', $render);
        $this->assertStringContainsString('string $fallback = self::LEGACY', $render);
    }

    public function testUntypedFloatDefaultRoundTripsAsBareNumericLiteral()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('untypedFloatDefault');
        $render = (string) $method;

        $this->assertStringContainsString('$noType = 1.5', $render);
        $this->assertStringNotContainsString('$noType = \'1.5\'', $render);
    }

    public function testPromotedConstructorParametersRoundTripWithVisibilityAndReadonly()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\PromotedPropertyTestClass');
        $method = $class->getMethod('__construct');
        $render = (string) $method;

        $this->assertStringContainsString('protected int $x = 1', $render);
        $this->assertStringContainsString("private readonly string \$y = 'a'", $render);
        $this->assertStringContainsString('public bool $flag = false', $render);

        $tmpFile = sys_get_temp_dir() . '/pop-code-promoted-ctor-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\nclass Tmp {\n" . $render . "\n}\n");
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output));
    }

    public function testVariadicParameterRoundTripsCorrectly()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('sum');
        $this->assertStringContainsString('function sum(int ...$numbers)', (string) $method);
    }

    public function testByRefParameterRoundTripsCorrectly()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('increment');
        $this->assertStringContainsString('function increment(int &$counter)', (string) $method);
    }

    public function testVariadicAndByRefCombinedRoundTripCorrectly()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('collectByRef');
        $this->assertStringContainsString('function collectByRef(&...$items)', (string) $method);
    }

    public function testUnionAndIntersectionParameterAndReturnTypesRoundTripCorrectly()
    {
        // Previously: MethodReflection's parameter loop silently dropped union/intersection
        // parameter types (a method_exists('getName') guard prevented a crash but discarded the
        // type entirely), and neither the parameter nor the return-type branch handled
        // intersection types at all.
        $class = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');

        $union = $class->getMethod('unionTyped');
        $render = (string) $union;
        $this->assertTrue(
            str_contains($render, 'int|string $x') || str_contains($render, 'string|int $x'),
            'Union parameter type was dropped: ' . $render
        );
        $this->assertStringContainsString('string|null $y = null', $render);
        $this->assertTrue(
            str_contains($render, '): int|string') || str_contains($render, '): string|int'),
            'Union return type was dropped: ' . $render
        );

        $intersection = $class->getMethod('intersectionTyped');
        $intersectionRender = (string) $intersection;
        $this->assertStringContainsString('Countable&Traversable $x', $intersectionRender);
        $this->assertStringContainsString('): Countable&Traversable', $intersectionRender);
    }

    public function testMethodAndParameterAttributesAreDetected()
    {
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\AttributedTestClass');
        $method = $class->getMethod('greet');
        $render = (string) $method;

        $this->assertTrue($method->hasAttribute('TagAttribute'));
        $this->assertStringContainsString("#[TagAttribute('method')]", $render);
        $this->assertStringContainsString("#[TagAttribute('param')] string \$name", $render);
    }

    public function testTypedParamDescriptionsSurviveTheDocblockAndArgumentSync()
    {
        // Three defects in one scenario, all previously broken: (1) DocblockReflection didn't
        // truncate the variable name after extracting the description, corrupting 'var' and
        // defeating the stale-@param dedup; (2) DocblockGenerator's render had no space before
        // the description; (3) even once (1) was fixed, addArgument()'s remove+re-add of the
        // param entry (needed for the dedup fix) had no way to preserve the description that
        // MethodReflection had already parsed from the real source docblock, silently dropping it.
        $class  = Reflection::createClass('Pop\Code\Test\TestAssets\ModernTestClass');
        $method = $class->getMethod('documentedGreeting');
        $render = (string) $method;

        $this->assertEquals(2, substr_count($render, '@param'));
        $this->assertStringContainsString('$name The name to use', $render);
        $this->assertStringContainsString('$qty How many', $render);
        $this->assertStringContainsString('@return string The greeting', $render);
    }

}
