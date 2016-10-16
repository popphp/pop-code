<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;

class DocblockTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $doc = new Generator\DocblockGenerator('This is a description', '    ');
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $doc);
        $this->assertEquals('This is a description', $doc->getDesc());
        $this->assertEquals('    ', $doc->getIndent());
    }

    public function testTags()
    {
        $doc = new Generator\DocblockGenerator('This is a description', '    ');
        $doc->setTag('foo', 'bar');
        $doc->setTags([
            'bar'   => 'baz',
            'hello' => 'world'
        ]);
        $this->assertEquals('bar', $doc->getTag('foo'));
        $this->assertEquals('baz', $doc->getTag('bar'));
        $this->assertEquals('world', $doc->getTag('hello'));
    }

    public function testParams()
    {
        $doc = new Generator\DocblockGenerator('This is a description', '    ');
        $doc->setParam('string', 'foo', 'This is a string');
        $doc->setParams([
            'type' => 'int',
            'var'  => 'bar',
            'desc' => 'This is an integer'
        ]);
        $this->assertEquals('string', $doc->getParam(0)['type']);
        $this->assertEquals('int', $doc->getParam(1)['type']);
    }

    public function testReturn()
    {
        $doc = new Generator\DocblockGenerator('This is a description', '    ');
        $doc->setParam('string', 'foo');
        $doc->setReturn('string', 'Returns string');
        $this->assertEquals('string', $doc->getReturn()['type']);
        $this->assertContains('* @return string Returns string', (string)$doc);
    }

    public function testRender()
    {
        $doc = new Generator\DocblockGenerator(
            "This is a long description. This is a long description. This is a long description. This is a long description. This is a long description.\nThis is a long description. This is a long description. This is a long description. This is a long description. This is a long description.\nThis is a long description. This is a long description. This is a long description. This is a long description. This is a long description.",
            '    '
        );
        $doc->setTag('foo', 'bar');
        $doc->setTags([
            'bar'    => 'baz',
            'throws' => 'Exception'
        ]);
        $doc->setParam('string', 'foo', 'This is a string');
        $doc->setParams([
            'type' => 'int',
            'var'  => 'bar',
            'desc' => 'This is an integer'
        ]);
        $doc->setReturn('string');

        ob_start();
        $doc->render();
        $result = ob_get_clean();
        $string = (string)$doc;

        $this->assertContains('* @foo    bar', $result);
        $this->assertContains('* @foo    bar', $string);
    }

    public function testParseException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $doc = Generator\DocblockGenerator::parse('bad docblock');
    }

}