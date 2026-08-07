<?php

namespace Pop\Code\Test\Reflection\Support;

use Pop\Code\Reflection\Support\SourceBodyExtractor;
use PHPUnit\Framework\TestCase;

class SourceBodyExtractorTest extends TestCase
{

    protected static string $file;

    public static function setUpBeforeClass(): void
    {
        self::$file = __DIR__ . '/../../tmp/source-body-extractor-fixture.php';
        file_put_contents(self::$file, <<<'CODE'
<?php
function extractorFixtureFunction($var)
{
    echo $var;
}

class ExtractorFixtureClass
{
    public function extractorFixtureMethod($var)
    {
        echo $var;
    }

    public function extractorFixtureWrapMethod(string $close = ')')
    {
        return $close;
    }
}

function extractorFixtureWrapFunction(string $close = ')')
{
    return $close;
}
CODE
        );
        require self::$file;
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::$file);
    }

    public function testExtractsFunctionBodyWithoutBraceStripping()
    {
        $reflection = new \ReflectionFunction('extractorFixtureFunction');
        $body       = SourceBodyExtractor::extract($reflection, false);
        $this->assertStringContainsString('echo $var;', $body);
    }

    public function testExtractsMethodBodyWithBraceStripping()
    {
        $reflection = new \ReflectionMethod('ExtractorFixtureClass', 'extractorFixtureMethod');
        $body       = SourceBodyExtractor::extract($reflection, true);
        $this->assertStringContainsString('echo $var;', $body);
        $this->assertStringNotContainsString('{', $body);
        $this->assertStringNotContainsString('}', $body);
    }

    public function testExtractsMethodBodyWhenParameterListContainsClosingParenInStringLiteral()
    {
        $reflection = new \ReflectionMethod('ExtractorFixtureClass', 'extractorFixtureWrapMethod');
        $body       = SourceBodyExtractor::extract($reflection, true);
        $this->assertNotNull($body);
        $this->assertStringContainsString('return $close;', $body);
        $this->assertStringNotContainsString('{', $body);
        $this->assertStringNotContainsString('}', $body);
    }

    public function testExtractsFunctionBodyWhenParameterListContainsClosingParenInStringLiteral()
    {
        $reflection = new \ReflectionFunction('extractorFixtureWrapFunction');
        $body       = SourceBodyExtractor::extract($reflection, false);
        $this->assertNotNull($body);
        $this->assertStringContainsString('return $close;', $body);
    }

}
