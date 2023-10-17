<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class TraitGeneratorTest extends TestCase
{

    public function testRender()
    {
        $trait = new Generator\TraitGenerator('FooTrait');

        $trait->addConstants([
            new Generator\ConstantGenerator('SOME_CONSTANT', 'string', 'STRING'),
            new Generator\ConstantGenerator('SOME_OTHER_CONSTANT', 'string', 'STRING')
        ]);

        $trait->addProperties([
            new Generator\PropertyGenerator('prop', 'string', 'STRING'),
            new Generator\PropertyGenerator('otherProp', 'string', 'STRING'),
            new Generator\PropertyGenerator('someProp', 'string', 'STRING')
        ]);

        $trait->addMethods([
            (new Generator\MethodGenerator('foo'))->setAsAbstract(true),
            (new Generator\MethodGenerator('bar'))->setAsAbstract(true)
        ]);

        $trait->addUse('OtherTrait');

        $this->assertTrue($trait->hasProperty('someProp'));
        $this->assertInstanceOf('Pop\Code\Generator\PropertyGenerator', $trait->getProperty('someProp'));
        $this->assertEquals(3, count($trait->getProperties()));

        $trait->removeProperty('someProp');
        $this->assertFalse($trait->hasProperty('someProp'));
        $this->assertEquals(2, count($trait->getProperties()));

        $this->assertTrue($trait->hasUse('OtherTrait'));
        $this->assertEquals(1, count($trait->getUses()));

        $render = (string)$trait;

        $this->assertStringContainsString('trait FooTrait', $render);
        $this->assertStringContainsString('use OtherTrait;', $render);
        $this->assertStringContainsString("const SOME_CONSTANT = 'STRING';", $render);
        $this->assertStringContainsString("const SOME_OTHER_CONSTANT = 'STRING';", $render);
        $this->assertStringContainsString("public \$prop = 'STRING';", $render);
        $this->assertStringContainsString("public \$otherProp = 'STRING';", $render);
        $this->assertStringContainsString('abstract public function foo();', $render);
        $this->assertStringContainsString('abstract public function bar();', $render);
    }

}