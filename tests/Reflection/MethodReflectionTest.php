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

}
