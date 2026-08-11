<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class NamespaceGeneratorTest extends TestCase
{

    public function testRender()
    {
        $namespace = new Generator\NamespaceGenerator('MyApp');
        $namespace->addUses([
            0   => 'MyApp\Foo',
            'B' => 'MyApp\Foo\Bar'
        ]);

        $render = (string)$namespace;

        $this->assertStringContainsString('namespace MyApp;', $render);
        $this->assertStringContainsString('use MyApp\Foo;', $render);
        $this->assertStringContainsString('use MyApp\Foo\Bar as B;', $render);
    }

    public function testRenderPreservesAUserSetDocblock()
    {
        // Previously render() unconditionally created a fresh docblock, discarding any docblock
        // a caller had set via DocblockTrait's setDesc()/setDocblock() despite this class mixing
        // that trait in.
        $namespace = new Generator\NamespaceGenerator('MyApp');
        $namespace->setDesc('Custom namespace description');

        $render = (string) $namespace;

        $this->assertStringContainsString('Custom namespace description', $render);
        $this->assertStringContainsString('@namespace', $render);
        $this->assertStringContainsString('namespace MyApp;', $render);
    }

    public function testGetOutputReturnsNullBeforeRender()
    {
        // Previously getOutput() was declared to return non-nullable string while $output is
        // initialized to null -- calling it before render() threw a TypeError. This is a
        // base-class (AbstractGenerator) contract, verified here via a representative generator.
        $namespace = new Generator\NamespaceGenerator('MyApp');
        $this->assertNull($namespace->getOutput());
        $this->assertFalse($namespace->hasOutput());
    }
}