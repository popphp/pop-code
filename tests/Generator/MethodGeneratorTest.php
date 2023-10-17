<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class MethodGeneratorTest extends TestCase
{

    public function testAbstract()
    {
        $method = new Generator\MethodGenerator('foo');
        $method->addArgument('var');
        $method->setAsAbstract(true);
        $this->assertStringContainsString('abstract public function foo($var);', (string)$method);
    }

    public function testStatic()
    {
        $method = new Generator\MethodGenerator('foo');
        $method->addArgument('var');
        $method->setAsStatic(true);
        $this->assertTrue($method->isStatic());
        $this->assertStringContainsString('public static function foo($var)', (string)$method);
    }

    public function testPublic()
    {
        $method = new Generator\MethodGenerator('foo');
        $method->setAsPublic();
        $this->assertTrue($method->isPublic());
        $this->assertEquals('public', $method->getVisibility());
        $this->assertStringContainsString('public function foo', (string)$method);
    }

    public function testProtected()
    {
        $method = new Generator\MethodGenerator('foo');
        $method->setAsProtected();
        $this->assertTrue($method->isProtected());
        $this->assertEquals('protected', $method->getVisibility());
        $this->assertStringContainsString('protected function foo', (string)$method);
    }

    public function testPrivate()
    {
        $method = new Generator\MethodGenerator('foo');
        $method->setAsPrivate();
        $this->assertTrue($method->isPrivate());
        $this->assertEquals('private', $method->getVisibility());
        $this->assertStringContainsString('private function foo', (string)$method);
    }

    public function testVisibilityException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $method = new Generator\MethodGenerator('foo');
        $method->setVisibility('bad');
    }

}