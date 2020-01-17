<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class DocblockGeneratorTest extends TestCase
{

    public function testDesc()
    {
        $docblock = new Generator\DocblockGenerator('Description');
        $this->assertTrue($docblock->hasDesc());
    }

    public function testTags()
    {
        $docblock = new Generator\DocblockGenerator('Description');
        $docblock->addTags([
            'foo' => 'bar'
        ]);
        $this->assertTrue($docblock->hasTag('foo'));
    }

    public function testParams()
    {
        $docblock = new Generator\DocblockGenerator('Description');
        $docblock->addParams([
            'type' => 'string',
            'var'  => 'foo',
            'desc' => 'Foo var'

        ]);
        $this->assertTrue($docblock->hasParam(0));
    }

    public function testRender()
    {
        $docblock = new Generator\DocblockGenerator();
        $docblock->setDesc('This is a long description. This is a long description. This is a long description. This is a long description. This is a long description. This is a long description. ');
        $docblock->addParams([
            'type' => 'string',
            'var'  => 'foo',
            'desc' => 'Foo var'
        ]);
        $docblock->setThrows('Exception', 'Error occurred');
        $docblock->setReturn('array');
        $this->assertTrue($docblock->hasThrows());
        $this->assertEquals('Exception', $docblock->getThrows()['type']);

        $render = (string)$docblock;
        $this->assertContains('    /**', $render);
        $this->assertContains('     * This is a long description. This is a long description. This is a long', $render);
        $this->assertContains('     * description. This is a long description. This is a long description.', $render);
        $this->assertContains('     * This is a long description.', $render);
        $this->assertContains('     * ', $render);
        $this->assertContains('     * @param  string  fooFoo var', $render);
        $this->assertContains('     * @throws Exception Error occurred', $render);
        $this->assertContains('     * @return array', $render);
        $this->assertContains('     */', $render);
    }

}