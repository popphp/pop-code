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

    public function testUnionTypeFormatsUsingTheValuesActualType()
    {
        // Previously a union type string (e.g. 'int|string') matched none of the single-type
        // checks, so it always fell through to the string-quoting branch regardless of the
        // value's real type -- silently coercing e.g. an int|string default that's actually an
        // int into a quoted string literal ('1' instead of 1).
        $this->assertEquals('1', ValueFormatter::format(1, 'int|string'));
        $this->assertEquals("'1'", ValueFormatter::format('1', 'string|int'));
        $this->assertEquals('true', ValueFormatter::format(true, 'bool|int'));
        $this->assertEquals('1.5', ValueFormatter::format(1.5, 'float|string'));
    }

    public function testUnionTypeWithNoMatchingMemberStillFormatsAsString()
    {
        // A union type where the value's actual type isn't one of the listed members (e.g. a
        // value that doesn't correspond to any declared member) should fall back to the
        // pre-existing catch-all behavior rather than throwing or misbehaving.
        $this->assertEquals("'x'", ValueFormatter::format('x', 'int|float'));
    }

}
