<?php

namespace Pop\Code\Test;

use Pop\Code;

class CodeTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $code = new Code\Generator(__DIR__ . '/TestAsset/hello.php');
        $code->appendToBody("echo 'Hello Back!';");
        $this->assertInstanceOf('Pop\Code\Generator', $code);
        $this->assertContains("echo 'Hello World!';", $code->getBody());
        $this->assertContains("echo 'Hello Back!';", $code->getBody());
        $this->assertEquals(__DIR__ . '/TestAsset/hello.php', $code->getFullpath());
    }

    public function testCreateClass()
    {
        $code = new Code\Generator('foo.php', Code\Generator::CREATE_CLASS);
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $code->code());
    }

    public function testCreateInterface()
    {
        $code = new Code\Generator('foo.php', Code\Generator::CREATE_INTERFACE);
        $this->assertInstanceOf('Pop\Code\Generator\InterfaceGenerator', $code->code());
    }

    public function testCloseTag()
    {
        $code = new Code\Generator('foo.php');
        $code->setCloseTag(true);
        $this->assertTrue($code->hasCloseTag());
    }

    public function testSetAndGetIndent()
    {
        $code = new Code\Generator('foo.php');
        $code->setIndent('    ');
        $this->assertEquals('    ', $code->getIndent());
    }

    public function testSetAndGetNamespace()
    {
        $code = new Code\Generator('foo.php');
        $code->setNamespace(new Code\Generator\NamespaceGenerator('foo'));
        $this->assertInstanceOf('Pop\Code\Generator\NamespaceGenerator', $code->getNamespace());
    }

    public function testSetAndGetDocblock()
    {
        $code = new Code\Generator('foo.php');
        $code->setDocblock(new Code\Generator\DocblockGenerator('description'));
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $code->getDocblock());
    }

    public function testRead()
    {
        $code = new Code\Generator(__DIR__ . '/TestAsset/hello.php');
        $this->assertContains("echo 'Hello World!';", $code->read());
        $this->assertEquals('Hello World!', $code->read(12, 12));
    }

    public function testRender()
    {
        $code = new Code\Generator('foo.php');
        $code->setBody("echo 'Hello World!';");
        $this->assertContains("echo 'Hello World!';", $code->render(true));
        $this->assertContains("echo 'Hello World!';", $code->read());
        $this->assertEquals('Hello World!', $code->read(13, 12));
    }

    public function testRenderWidthNamespace()
    {
        $code = new Code\Generator('Foo.php', Code\Generator::CREATE_CLASS);
        $code->setNamespace(new Code\Generator\NamespaceGenerator('Foo'));
        $code->setDocblock(new Code\Generator\DocblockGenerator('description'));
        $code->setCloseTag(true);

        ob_start();
        $code->render();
        $result = ob_get_clean();

        $this->assertContains('class Foo', $code->render(true));
        $this->assertContains('class Foo', $result);
        $this->assertContains('class Foo', (string)$code);
    }

    public function testSave()
    {
        $code = new Code\Generator(__DIR__ . '/foo.php');
        $code->setBody("echo 'Hello World!';");
        $code->save();

        $code->appendToBody("echo 'Hello World!';");
        $code->save(null, true);
        $this->assertTrue(file_exists(__DIR__ . '/foo.php'));
        $this->assertContains("echo 'Hello World!';", file_get_contents(__DIR__ . '/foo.php'));

        unlink(__DIR__ . '/foo.php');
    }

}