<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{

    public function testConstructor()
    {
        $code = new Generator(new Generator\ClassGenerator('Foo'));
        $this->assertInstanceOf('Pop\Code\Generator', $code);
        $this->assertTrue($code->hasCode());
        $this->assertTrue(is_array($code->code()));

        $codeObjects = $code->getCode();
        $this->assertTrue(is_array($codeObjects));
        $this->assertEquals(1, count($codeObjects));
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $codeObjects[0]);
    }

    public function testConstructorArray()
    {
        $function1 = new Generator\FunctionGenerator('foo');
        $function2 = new Generator\FunctionGenerator('bar');
        $code = new Generator([$function1, $function2]);
        $this->assertInstanceOf('Pop\Code\Generator', $code);
        $this->assertTrue($code->hasCode());
    }

    public function testConstructorException()
    {
        $this->expectException('Pop\Code\Exception');
        $code = new Generator('bad code');
    }

    public function testCloseTag()
    {
        $code = new Generator();
        $code->setCloseTag(true);
        $this->assertTrue($code->hasCloseTag());
    }

    public function testEnv()
    {
        $code = new Generator();
        $code->setEnv('#!/usr/bin/php');
        $this->assertTrue($code->hasEnv());
        $this->assertEquals('#!/usr/bin/php', $code->getEnv());
    }

    public function testFilename()
    {
        $code = new Generator();
        $code->setFilename('file.php');
        $this->assertTrue($code->hasFilename());
        $this->assertEquals('file.php', $code->getFilename());
    }

    public function testNamespace1()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function = new Generator\FunctionGenerator('foo');
        $function->addArgument('name');
        $function->setBody($functionBody);

        $code = new Generator();
        $code->addCodeObject($function, 'MyNamespace');

        $this->assertStringContainsString('namespace MyNamespace {', $code->render());
    }

    public function testNamespace2()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function = new Generator\FunctionGenerator('foo');
        $function->addArgument('name');
        $function->setBody($functionBody);

        $code = new Generator();
        $code->addCodeObjects(['MyNamespace' => $function]);

        $this->assertStringContainsString('namespace MyNamespace {', $code->render());
    }

    public function testNamespace3()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function1 = new Generator\FunctionGenerator('foo');
        $function1->addArgument('name');
        $function1->setBody($functionBody);

        $function2 = new Generator\FunctionGenerator('bar');
        $function2->addArgument('name');
        $function2->setBody($functionBody);

        $code = new Generator();
        $code->addCodeObjects(['MyNamespace' => [$function1, $function2]]);

        $this->assertStringContainsString('namespace MyNamespace {', $code->render());
    }

    public function testWriteToFile1()
    {
        $body = new Generator\BodyGenerator();
        $body->setBody("'echo 'Hello World.';");
        $code   = new Generator($body);
        $code->writeToFile(__DIR__ . '/tmp/code.php');

        $this->assertFileExists(__DIR__ . '/tmp/code.php');

        unlink(__DIR__ . '/tmp/code.php');
    }

    public function testWriteToFile2()
    {
        $body = new Generator\BodyGenerator();
        $body->setBody("'echo 'Hello World.';");
        $code   = new Generator($body);
        $code->setFilename(__DIR__ . '/tmp/code.php');
        $code->writeToFile();

        $this->assertFileExists(__DIR__ . '/tmp/code.php');

        unlink(__DIR__ . '/tmp/code.php');
    }

    public function testWriteToFileException()
    {
        $this->expectException('Pop\Code\Exception');
        $body = new Generator\BodyGenerator();
        $body->setBody("'echo 'Hello World.';");
        $code   = new Generator($body);
        $code->writeToFile();
    }

    public function testRender()
    {
        $method1Body = <<<MTHD
echo \$baz;
echo 'something else 123';
MTHD;

        $method1 = new Generator\MethodGenerator('bar');
        $method1->addArgument('baz');
        $method1->setBody($method1Body);

        $method2Body = <<<MTHD
echo \$baz;
echo 'something else';
MTHD;

        $method2 = new Generator\MethodGenerator('printSomething');
        $method2->addArgument('str');
        $method2->setBody($method2Body);

        $property1 = new Generator\PropertyGenerator('myProp', 'string');
        $property2 = new Generator\PropertyGenerator('otherProp', 'array');

        $constant1 = new Generator\ConstantGenerator('SOME_STR', 'string', 'value');
        $constant2 = new Generator\ConstantGenerator('SOME_NUM', 'number', 123);

        $class = new Generator\ClassGenerator('Foo');
        $class->addMethod($method1);
        $class->addMethod($method2);
        $class->addProperty($property1);
        $class->addProperty($property2);
        $class->addConstant($constant1);
        $class->addConstant($constant2);

        $class->setAsAbstract();
        $class->setParent('AbstractFoo');
        $class->addInterface('FooInterface, SomeOtherInterface');
        $class->addUse('SomeTrait');

        $code   = new Generator($class);
        $code->setEnv('#!/usr/bin/php');
        $code->setCloseTag(true);
        $string = (string)$code;
        $render = $code->render();

        $this->assertEquals($string, $render);

        $this->assertStringContainsString('#!/usr/bin/php', $render);
        $this->assertStringContainsString('<?php', $render);
        $this->assertStringContainsString('abstract class Foo extends AbstractFoo implements FooInterface, SomeOtherInterface', $render);
        $this->assertStringContainsString('use SomeTrait;', $render);
        $this->assertStringContainsString('const SOME_STR = \'value\';', $render);
        $this->assertStringContainsString('const SOME_NUM = \'123\';', $render);
        $this->assertStringContainsString('public $myProp = null;', $render);
        $this->assertStringContainsString('public $otherProp = [];', $render);
        $this->assertStringContainsString('public function bar($baz)', $render);
        $this->assertStringContainsString('public function printSomething($str)', $render);
        $this->assertStringContainsString('?>', $render);
    }

    public function testRenderWithNamespaces()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function1 = new Generator\FunctionGenerator('foo');
        $function1->addArgument('name');
        $function1->setBody($functionBody);

        $function2 = new Generator\FunctionGenerator('bar');
        $function2->addArgument('name');
        $function2->setBody($functionBody);

        $function3 = new Generator\FunctionGenerator('baz');
        $function3->addArgument('name');
        $function3->setBody($functionBody);

        $function4 = new Generator\FunctionGenerator('test');
        $function4->addArgument('name');
        $function4->setBody($functionBody);

        $code = new Generator();
        $code->addCodeObjects(['MyNamespace' => $function1]);
        $code->addCodeObject($function3, '*');
        $code->addCodeObject($function4);
        $code->addCodeObjects(['MyNamespace' => $function2]);

        $render = $code->render();

        $this->assertStringContainsString('namespace MyNamespace {', $render);
        $this->assertStringContainsString('namespace {', $render);
    }

    public function testOutputToHttp1()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function1 = new Generator\FunctionGenerator('foo');
        $function1->addArgument('name');
        $function1->setBody($functionBody);

        $function2 = new Generator\FunctionGenerator('bar');
        $function2->addArgument('name');
        $function2->setBody($functionBody);

        $function3 = new Generator\FunctionGenerator('baz');
        $function3->addArgument('name');
        $function3->setBody($functionBody);

        $function4 = new Generator\FunctionGenerator('test');
        $function4->addArgument('name');
        $function4->setBody($functionBody);

        $code = new Generator();
        $code->addCodeObjects(['MyNamespace' => $function1]);
        $code->addCodeObject($function3, '*');
        $code->addCodeObject($function4);
        $code->addCodeObjects(['MyNamespace' => $function2]);

        ob_start();
        $code->outputToHttp();
        $results = ob_get_clean();

        $this->assertStringContainsString('<?php', $results);
    }

    public function testOutputToHttp2()
    {
        $functionBody = <<<FUNC
echo 'You have submitted your name!' . PHP_EOL;
echo \$name . PHP_EOL;
FUNC;

        $function1 = new Generator\FunctionGenerator('foo');
        $function1->addArgument('name');
        $function1->setBody($functionBody);

        $function2 = new Generator\FunctionGenerator('bar');
        $function2->addArgument('name');
        $function2->setBody($functionBody);

        $function3 = new Generator\FunctionGenerator('baz');
        $function3->addArgument('name');
        $function3->setBody($functionBody);

        $function4 = new Generator\FunctionGenerator('test');
        $function4->addArgument('name');
        $function4->setBody($functionBody);

        $code = new Generator();
        $code->setFilename('code.php');
        $code->addCodeObjects(['MyNamespace' => $function1]);
        $code->addCodeObject($function3, '*');
        $code->addCodeObject($function4);
        $code->addCodeObjects(['MyNamespace' => $function2]);

        ob_start();
        $code->outputToHttp();
        $results = ob_get_clean();

        $this->assertStringContainsString('<?php', $results);
    }

}
