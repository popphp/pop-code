<?php

namespace Pop\Code\Test;

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
            new Generator\PropertyGenerator('otherProp', 'string', 'STRING')
        ]);

        $trait->addMethods([
            (new Generator\MethodGenerator('foo'))->setAsAbstract(true),
            (new Generator\MethodGenerator('bar'))->setAsAbstract(true)
        ]);

        $trait->addUse('OtherTrait');

        $render = (string)$trait;

        $this->assertContains('trait FooTrait', $render);
        $this->assertContains('use OtherTrait;', $render);
        $this->assertContains("const SOME_CONSTANT = 'STRING';", $render);
        $this->assertContains("const SOME_OTHER_CONSTANT = 'STRING';", $render);
        $this->assertContains("public \$prop = 'STRING';", $render);
        $this->assertContains("public \$otherProp = 'STRING';", $render);
        $this->assertContains('abstract public function foo();', $render);
        $this->assertContains('abstract public function bar();', $render);
    }

}