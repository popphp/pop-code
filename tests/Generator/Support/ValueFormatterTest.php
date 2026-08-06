<?php

namespace Pop\Code\Test\Generator\Support;

use Pop\Code\Generator\Support\ValueFormatter;
use PHPUnit\Framework\TestCase;

class ValueFormatterTest extends TestCase
{

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

}
