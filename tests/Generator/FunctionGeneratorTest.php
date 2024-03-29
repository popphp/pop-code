<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class FunctionGeneratorTest extends TestCase
{

    public function testClosure()
    {
        $function = new Generator\FunctionGenerator('foo', true);
        $function->setBody("echo 'Hello World!;");
        $this->assertTrue($function->isClosure());
        $this->assertStringContainsString('};', $function->render());
    }

    public function testRenderException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $function = new Generator\FunctionGenerator();
        $function->render();
    }

    public function testAddArgumentsException()
    {
        $this->expectException('InvalidArgumentException');
        $function = new Generator\FunctionGenerator();
        $function->addParameters([
            'name'  => 'bar',
            'value' => 'hello',
            'type'  => 'string'
        ]);
    }

    public function testArguments()
    {
        $function = new Generator\FunctionGenerator('someFunc');
        $function->addParameter('foo', '123', 'int');
        $function->addParameters([
            [
                'name'  => 'bar',
                'value' => 'hello',
                'type'  => 'string'
            ]
        ]);

        $this->assertTrue($function->hasParameters());
        $this->assertTrue($function->hasParameter('foo'));
        $this->assertEquals(2, count($function->getParameters()));
        $this->assertEquals('123', $function->getParameter('foo')['value']);
        $this->assertStringContainsString("function someFunc(int \$foo = 123, string \$bar = hello)", (string)$function);
    }

    public function testReturnTypes()
    {
        $function = new Generator\FunctionGenerator('someFunc');
        $function->addReturnTypes(['string', 'null']);
        $this->assertTrue($function->hasReturnTypes());
        $this->assertTrue($function->hasReturnType('string'));
        $this->assertCount(2, $function->getReturnTypes());
        $this->assertStringContainsString("): string|null", (string)$function);
    }

}