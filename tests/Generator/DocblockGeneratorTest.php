<?php

namespace Pop\Code\Test\Generator;

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
        $this->assertTrue($docblock->hasTags());
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
        $this->assertTrue($docblock->hasTags());
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
        $this->assertStringContainsString('    /**', $render);
        $this->assertStringContainsString('     * This is a long description. This is a long description. This is a long', $render);
        $this->assertStringContainsString('     * description. This is a long description. This is a long description.', $render);
        $this->assertStringContainsString('     * This is a long description.', $render);
        $this->assertStringContainsString('     * ', $render);
        $this->assertStringContainsString('     * @param  string  fooFoo var', $render);
        $this->assertStringContainsString('     * @throws Exception Error occurred', $render);
        $this->assertStringContainsString('     * @return array', $render);
        $this->assertStringContainsString('     */', $render);
    }

    public function testRemoveParamRemovesTheFirstMatchByVarName()
    {
        $docblock = new Generator\DocblockGenerator();
        $docblock->addParam('string', '$foo');
        $docblock->addParam('int', '$bar');

        $docblock->removeParam('$foo');

        $this->assertEquals('int', $docblock->getParam(0)['type']);
        $this->assertEquals('$bar', $docblock->getParam(0)['var']);
        $this->assertNull($docblock->getParam(1));
    }

    public function testRemoveParamIsANoOpWhenNoMatchExists()
    {
        $docblock = new Generator\DocblockGenerator();
        $docblock->addParam('string', '$foo');

        $docblock->removeParam('$doesNotExist');

        $this->assertEquals('$foo', $docblock->getParam(0)['var']);
    }

}
