<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class EnumReflectionTest extends TestCase
{

    use RoundTripExecutionTrait;

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
        $this->assertStringContainsString('StatusEnum::Active', (string) $enum->getConstant('DEFAULT'));
    }

    public function testDocumentedCaseDocblockIsIndentedToMatchEnumBody()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');
        $render = (string) $enum;

        $this->assertStringContainsString("    /**\n     * The active status\n     */\n    case Active", $render);
    }

    public function testMethodsAreDetected()
    {
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');

        $this->assertTrue($enum->hasMethod('color'));
        $this->assertTrue($enum->hasMethod('fromLabel'));
    }

    public function testInternalCasesFromTryFromMethodsAreNotReDeclared()
    {
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');

        $this->assertFalse($enum->hasMethod('cases'));
        $this->assertFalse($enum->hasMethod('from'));
        $this->assertFalse($enum->hasMethod('tryFrom'));
    }

    public function testPureEnumRoundTripsCorrectly()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\PureEnum');
        $render = (string) $enum;

        $this->assertFalse($enum->hasBackingType());
        $this->assertStringContainsString('case A;', $render);
        $this->assertStringContainsString('case B;', $render);
    }

    /**
     * Regenerated code that merely passes `php -l` (a syntax-only check) can still fatal the
     * instant it is actually loaded -- e.g. PHP's own synthesized cases()/from()/tryFrom() methods
     * being re-emitted as user-declared methods causes "Cannot redeclare" at compile time, which
     * `php -l` does not detect. This test spawns a real subprocess that requires the regenerated
     * file and exercises real behavior (including a trait-provided method), so a regression here
     * fails loudly instead of silently passing a lint-only check.
     */
    public function testBackedEnumRegeneratesAsValidPhpThatLoadsAndBehavesCorrectly()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\StatusEnum');
        $render = (string) $enum;

        // The render references ColorfulInterface and StatusHelperTrait but does not itself
        // define them (they live alongside the enum in the fixture file). Extract everything
        // up to the enum declaration from the original fixture source (namespace + interface +
        // trait) and prepend it, so the regenerated enum has its dependencies available without
        // also re-declaring the original StatusEnum class itself.
        $fixtureSource = file_get_contents(__DIR__ . '/../TestAssets/StatusEnum.php');
        $cutPoint      = strpos($fixtureSource, 'enum StatusEnum');
        $this->assertNotFalse($cutPoint, 'Could not locate the enum declaration in the StatusEnum fixture.');
        $preamble = substr($fixtureSource, 0, $cutPoint);

        $enumFile = sys_get_temp_dir() . '/pop-code-enum-' . uniqid() . '.php';
        file_put_contents($enumFile, $preamble . $render);

        $checkScript = sys_get_temp_dir() . '/pop-code-enum-check-' . uniqid() . '.php';
        file_put_contents($checkScript, '<?php' . PHP_EOL
            . 'require ' . var_export($enumFile, true) . ';' . PHP_EOL
            . 'use Pop\Code\Test\TestAssets\StatusEnum;' . PHP_EOL
            . 'if (StatusEnum::Active->color() !== "green") { fwrite(STDERR, "color() mismatch\n"); exit(2); }' . PHP_EOL
            . 'if (StatusEnum::Active->isActive() !== true) { fwrite(STDERR, "isActive() mismatch\n"); exit(3); }' . PHP_EOL
            . 'echo "OK";' . PHP_EOL
            . 'exit(0);' . PHP_EOL
        );

        exec('php ' . escapeshellarg($checkScript) . ' 2>&1', $output, $exitCode);
        unlink($enumFile);
        unlink($checkScript);

        $outputText = implode("\n", $output);
        $this->assertEquals(0, $exitCode, $outputText . "\n\n" . $render);
        $this->assertEquals('OK', $outputText);
    }

    /**
     * Simpler sibling of the above: PureEnum has no interface/trait dependencies, so this just
     * confirms the regenerated file actually *loads* (not merely lints) without a fatal error.
     */
    public function testPureEnumRegeneratesAsValidPhpThatLoadsWithoutFatal()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\PureEnum');
        $render = (string) $enum;

        $tmpFile = sys_get_temp_dir() . '/pop-code-enum-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\n" . $render);

        exec('php ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $exitCode);
        unlink($tmpFile);

        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $render);
    }

    public function testEnumLevelAttributeIsDetected()
    {
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\AttributedEnum');
        $this->assertTrue($enum->hasAttribute('TagAttribute'));
        $this->assertStringContainsString("#[TagAttribute('enum')]\nenum AttributedEnum", (string) $enum);
    }

    public function testCaseLevelAttributeIsDetected()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\AttributedEnum');
        $active = $enum->getCase('Active');

        $this->assertTrue($active->hasAttribute('TagAttribute'));
        $this->assertStringContainsString("#[TagAttribute('case')]", (string) $active);

        $inactive = $enum->getCase('Inactive');
        $this->assertFalse($inactive->hasAttribute('TagAttribute'));
    }

    public function testCollidingShortNamesAcrossEnumLevelAndCaseLevelFallBackToFqcn()
    {
        // The enum-level attribute and the case-level attribute share the same short name
        // (ForeignTagAttribute) from two different foreign namespaces, and must share one
        // NamespaceImportResolver so the collision is caught across both levels, not just within
        // one.
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\CollidingAttributesEnum');
        $render = (string) $enum;

        $this->assertTrue($enum->hasAttribute('ForeignTagAttribute'));
        $this->assertStringContainsString('\Pop\Code\Test\TestAssets\AttrsB\ForeignTagAttribute', $render);

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

    public function testSameNamespaceAttributeDoesNotGetARedundantUseImport()
    {
        // TagAttribute lives in the same namespace as AttributedEnum itself
        // (Pop\Code\Test\TestAssets) at both the enum level and the case level -- it resolves
        // correctly by its bare short name with no import, so EnumReflection must not add one.
        $enum = Reflection::createEnum('Pop\Code\Test\TestAssets\AttributedEnum');

        $this->assertTrue($enum->hasNamespace());
        $this->assertFalse($enum->getNamespace()->hasUse('Pop\Code\Test\TestAssets\TagAttribute'));
    }

    public function testAttributedEnumRegeneratesAsValidPhpThatLoads()
    {
        $enum   = Reflection::createEnum('Pop\Code\Test\TestAssets\AttributedEnum');
        $render = (string) $enum;

        [$exitCode, $output, $content] = $this->executeRenderedFragment($render);
        $this->assertEquals(0, $exitCode, implode("\n", $output) . "\n\n" . $content);
    }

}
