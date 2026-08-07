<?php

namespace Pop\Code\Test\Generator\Support;

use Pop\Code\Generator\Exception;
use Pop\Code\Generator\Literal;
use Pop\Code\Generator\Support\ValueFormatter;
use Pop\Code\Test\TestAssets\StatusEnum;
use PHPUnit\Framework\TestCase;

class ValueFormatterTest extends TestCase
{

    public function testFormatsEnumCaseAsShortClassNameDoubleColonCaseName()
    {
        $this->assertEquals('StatusEnum::Active', ValueFormatter::format(StatusEnum::Active));
    }

    public function testFormatsNullAsNullRegardlessOfType()
    {
        $this->assertEquals('null', ValueFormatter::format(null, 'string'));
        $this->assertEquals('null', ValueFormatter::format(null, 'array'));
        $this->assertEquals('null', ValueFormatter::format(null));
    }

    public function testFormatsScalarsByDeclaredType()
    {
        $this->assertEquals('5', ValueFormatter::format(5, 'int'));
        $this->assertEquals('5', ValueFormatter::format('5', 'int'));
        $this->assertEquals('5.5', ValueFormatter::format(5.5, 'float'));
        $this->assertEquals('true', ValueFormatter::format(true, 'bool'));
        $this->assertEquals('false', ValueFormatter::format(false, 'bool'));
        $this->assertEquals("'hello'", ValueFormatter::format('hello', 'string'));
    }

    public function testFormatsEmptyAndNonEmptyArrays()
    {
        $this->assertEquals('[]', ValueFormatter::format([], 'array'));

        $formatted = ValueFormatter::format([1, 2, 3], 'array');
        $this->assertStringStartsWith('[', $formatted);
        $this->assertStringEndsWith(']', $formatted);
        $this->assertStringContainsString('1,', $formatted);
    }

    public function testInfersTypeFromValueWhenNoTypeGiven()
    {
        $this->assertEquals('5', ValueFormatter::format(5));
        $this->assertEquals('true', ValueFormatter::format(true));
        $this->assertEquals("'hello'", ValueFormatter::format('hello'));
        $this->assertEquals('[]', ValueFormatter::format([]));
    }

    public function testInfersDoubleTypeAsBareNumericLiteral()
    {
        $this->assertEquals('1.5', ValueFormatter::format(1.5));
    }

    public function testEscapesSingleQuotesAndBackslashesInStrings()
    {
        $this->assertEquals("'it\\'s here'", ValueFormatter::format("it's here", 'string'));
        $this->assertEquals("'back\\\\slash'", ValueFormatter::format('back\\slash', 'string'));
    }

    public function testThrowsCatchableExceptionForUnstringableObject()
    {
        $this->expectException(Exception::class);
        ValueFormatter::format(new \stdClass());
    }

    public function testCompactModeRendersArraysOnASingleLine()
    {
        $formatted = ValueFormatter::format(['a', 'b', 'c'], 'array', '', true);
        $this->assertEquals("['a', 'b', 'c']", $formatted);
        $this->assertStringNotContainsString(PHP_EOL, $formatted);
    }

    public function testCompactModeRendersAssociativeArraysWithArrowSyntax()
    {
        $formatted = ValueFormatter::format(['name' => 'a', 'priority' => 5], 'array', '', true);
        $this->assertEquals("['name' => 'a', 'priority' => 5]", $formatted);
    }

    public function testCompactModeRendersNestedArraysOnASingleLine()
    {
        $formatted = ValueFormatter::format(['a', ['b', 'c']], 'array', '', true);
        $this->assertEquals("['a', ['b', 'c']]", $formatted);
        $this->assertStringNotContainsString(PHP_EOL, $formatted);
    }

    public function testCompactModeStillRendersEmptyArrayAsBareBrackets()
    {
        $this->assertEquals('[]', ValueFormatter::format([], 'array', '', true));
    }

    public function testLiteralValueIsEmittedVerbatim()
    {
        $this->assertEquals('self::FOO', ValueFormatter::format(new Literal('self::FOO')));
        $this->assertEquals('Status::Active', ValueFormatter::format(new Literal('Status::Active'), 'string'));
    }

}
