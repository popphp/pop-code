<?php

namespace Pop\Code\Test;

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

        $this->assertContains('namespace MyApp;', $render);
        $this->assertContains('use MyApp\Foo;', $render);
        $this->assertContains('use MyApp\Foo\Bar as B;', $render);
    }
}