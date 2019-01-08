<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{

    public function testConstructor()
    {
        $property = new Generator\PropertyGenerator('foo', 'string', 'Bar', 'protected');
        $property->setDesc('This is a property');
        $property->setIndent('    ');
        $property->setStatic(true);
        $this->assertInstanceOf('Pop\Code\Generator\PropertyGenerator', $property);
        $this->assertEquals('foo', $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('Bar', $property->getValue());
        $this->assertEquals('This is a property', $property->getDesc());
        $this->assertEquals('    ', $property->getIndent());
        $this->assertEquals('protected', $property->getVisibility());
        $this->assertTrue($property->isStatic());
    }

    public function testDocblock()
    {
        $property = new Generator\PropertyGenerator('foo', 'integer', 123);
        $property->setDocblock(new Generator\DocblockGenerator('This is a property'));
        $property->setDesc('Revised desc');
        $this->assertInstanceOf('Pop\Code\Generator\DocblockGenerator', $property->getDocblock());
        $this->assertEquals('Revised desc', $property->getDesc());
        $this->assertContains('* @var   int', (string)$property);
    }

    public function testRenderArray()
    {
        $property = new Generator\PropertyGenerator('foo', 'array', [
            'hello', 'world'
        ], 'protected');
        $property->setDesc('This is a property');
        $property->setIndent('    ');

        ob_start();
        $property->render();
        $result = ob_get_clean();
        $string = (string)$property;

        $this->assertContains('* This is a property', $result);
        $this->assertContains('* This is a property', $string);
        $this->assertContains('* @var   array', $result);
        $this->assertContains('* @var   array', $string);
    }

    public function testBoolean()
    {
        $property = new Generator\PropertyGenerator('foo', 'boolean', true, 'protected');
        $property->setDesc('This is a property');
        $property->setIndent('    ');

        ob_start();
        $property->render();
        $result = ob_get_clean();
        $string = (string)$property;

        $this->assertContains('* This is a property', $result);
        $this->assertContains('* This is a property', $string);
        $this->assertContains('* @var   boolean', $result);
        $this->assertContains('* @var   boolean', $string);
    }

}