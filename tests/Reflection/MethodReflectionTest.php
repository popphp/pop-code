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

}
