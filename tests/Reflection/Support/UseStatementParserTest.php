<?php

namespace Pop\Code\Test\Reflection\Support;

use Pop\Code\Reflection\Support\UseStatementParser;
use PHPUnit\Framework\TestCase;

class UseStatementParserTest extends TestCase
{

    public function testParsesPlainAndAliasedUses()
    {
        $source = <<<CODE
namespace Foo;

class Bar
{
    use SomeTrait;
    use Another\NamespacedTrait as Aliased;
}
CODE;

        $uses = UseStatementParser::parse($source);

        $this->assertArrayHasKey('SomeTrait', $uses);
        $this->assertNull($uses['SomeTrait']);
        $this->assertEquals('Aliased', $uses['Another\NamespacedTrait']);
    }

    public function testReturnsEmptyArrayWhenNoUses()
    {
        $this->assertEquals([], UseStatementParser::parse("namespace Foo;\n\nclass Bar {}\n"));
    }

}
