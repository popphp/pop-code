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
        $string = (string)$code;
        $render = $code->render();

        $this->assertEquals($string, $render);
        $this->assertContains('<?php', $render);
        $this->assertContains('abstract class Foo extends AbstractFoo implements FooInterface, SomeOtherInterface', $render);
        $this->assertContains('use SomeTrait;', $render);
        $this->assertContains('const SOME_STR = \'value\';', $render);
        $this->assertContains('const SOME_NUM = \'123\';', $render);
        $this->assertContains('public $myProp = null;', $render);
        $this->assertContains('public $otherProp = [];', $render);
        $this->assertContains('public function bar($baz)', $render);
        $this->assertContains('public function printSomething($str)', $render);
    }

}
