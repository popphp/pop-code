<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Generator;
use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class FunctionReflectionTest extends TestCase
{

    use RoundTripExecutionTrait;

    public function testUnnamedClosureRegeneratesAsValidPhpThatLoads()
    {
        // Previously FunctionReflection detected a closure by checking
        // $reflectionName === '{closure}' -- PHP 8.4 renamed closures to
        // '{closure:file:line}', so isClosure was always false and the mangled name got used as
        // a literal function name, producing a hard parse error on this library's own minimum
        // supported PHP version.
        $code = <<<'PHP'
<?php
$f = function(int $x) {
    return $x;
};
return $f;
PHP;
        $tmpFile = sys_get_temp_dir() . '/pop-code-closure-src-' . uniqid() . '.php';
        file_put_contents($tmpFile, $code);
        $closure = require $tmpFile;
        unlink($tmpFile);

        $function = Reflection::createFunction($closure);
        $this->assertTrue($function->isClosure());

        $render = (string) $function;
        $this->assertStringNotContainsString('{closure', $render);

        $tmp = sys_get_temp_dir() . '/pop-code-closure-render-' . uniqid() . '.php';
        file_put_contents($tmp, "<?php\n" . $render);
        exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $exitCode);
        unlink($tmp);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $render);
    }

    public function testNamedClosureAssignsToTheGivenVariableName()
    {
        $closure  = function(string $s) { return strtoupper($s); };
        $function = Reflection::createFunction($closure, 'shout');
        $render   = (string) $function;

        $this->assertStringContainsString('$shout = function(string $s)', $render);
    }

    public function testPlainDefaultValueRoundTripsCorrectly()
    {
        $code     = function(string $name = 'world') { return $name; };
        $function = Reflection::createFunction($code);
        $this->assertStringContainsString("string \$name = 'world'", (string) $function);
    }

    public function testConstantDefaultValueRoundTripsAsTheOriginalExpression()
    {
        // Declared via a plain (unnamespaced) source file rather than inline in this test class --
        // ReflectionParameter::getDefaultValueConstantName() reports a namespace-qualified form
        // for a global constant referenced from within a namespaced file (a PHP reflection
        // quirk, not something pop-code controls), which would make this assertion about the
        // exact rendered text unreliable if the closure were declared inline here instead.
        $code = <<<'PHP'
<?php
return function(string $sep = PHP_EOL) {
    return $sep;
};
PHP;
        $tmpFile = sys_get_temp_dir() . '/pop-code-const-default-src-' . uniqid() . '.php';
        file_put_contents($tmpFile, $code);
        $closure = require $tmpFile;
        unlink($tmpFile);

        $function = Reflection::createFunction($closure);
        $render   = (string) $function;

        $this->assertStringContainsString('string $sep = PHP_EOL', $render);
        $this->assertStringNotContainsString("'PHP_EOL'", $render);
    }

    public function testParameterAttributeIsDetected()
    {
        $code     = function(#[\Pop\Code\Test\TestAssets\TagAttribute('x')] string $x) { return $x; };
        $function = Reflection::createFunction($code);
        $this->assertStringContainsString("#[TagAttribute('x')] string \$x", (string) $function);
    }

    public function testUnionAndIntersectionParameterAndReturnTypesRoundTripCorrectly()
    {
        $code     = function(int|string $x): int|string { return $x; };
        $function = Reflection::createFunction($code);
        $render   = (string) $function;

        $this->assertTrue(str_contains($render, 'int|string $x') || str_contains($render, 'string|int $x'));
        $this->assertTrue(str_contains($render, '): int|string') || str_contains($render, '): string|int'));
    }

}
