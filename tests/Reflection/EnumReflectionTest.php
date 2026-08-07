<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class EnumReflectionTest extends TestCase
{

    public function testBackedEnumRoundTripsCorrectly()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');
        $render = (string) $enum;

        $this->assertInstanceOf('Pop\Code\Generator\EnumGenerator', $enum);
        $this->assertEquals('string', $enum->getBackingType());
        $this->assertTrue($enum->hasInterface('ColorfulInterface'));
        $this->assertStringNotContainsString('UnitEnum', $render);
        $this->assertStringNotContainsString('BackedEnum', $render);
        $this->assertStringContainsString('use StatusHelperTrait;', $render);
        $this->assertStringContainsString("case Active = 'active';", $render);
        $this->assertStringContainsString("case Inactive = 'inactive';", $render);
        $this->assertStringContainsString('The active status', $render);
    }

    public function testCaseIsNotDuplicatedAsAConstant()
    {
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');

        $this->assertFalse($enum->hasConstant('Active'));
        $this->assertFalse($enum->hasConstant('Inactive'));
        $this->assertTrue($enum->hasConstant('DEFAULT'));
    }

    public function testMethodsAreDetected()
    {
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');

        $this->assertTrue($enum->hasMethod('color'));
        $this->assertTrue($enum->hasMethod('fromLabel'));
    }

    public function testPureEnumRoundTripsCorrectly()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\PureEnum');
        $render = (string) $enum;

        $this->assertFalse($enum->hasBackingType());
        $this->assertStringContainsString('case A;', $render);
        $this->assertStringContainsString('case B;', $render);
    }

    public function testBackedEnumRegeneratesAsValidPhp()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');
        $render = (string) $enum;

        $tmpFile = sys_get_temp_dir() . '/pop-code-enum-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\n" . $render);
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $render);
    }

}
