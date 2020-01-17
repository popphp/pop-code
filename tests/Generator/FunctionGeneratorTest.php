<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class FunctionGeneratorTest extends TestCase
{

    public function testClosure()
    {
        $function = new Generator\FunctionGenerator('foo', true);
        $function->setBody("echo 'Hello World!;");
        $this->assertTrue($function->isClosure());
        $this->assertContains('};', $function->render());
    }

    public function testRenderException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $function = new Generator\FunctionGenerator();
        $function->render();
    }

}