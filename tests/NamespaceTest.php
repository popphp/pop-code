<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class NamespaceTest extends TestCase
{

    public function testConstructor()
    {
        $namespace = new Generator\NamespaceGenerator('MyNamespace');
        $this->assertInstanceOf('Pop\Code\Generator\NamespaceGenerator', $namespace);
        $this->assertEquals('MyNamespace', $namespace->getNamespace());
    }

    public function testRender()
    {
        $namespace = new Generator\NamespaceGenerator('MyNamespace');
        $namespace->setUse('My\Other\Component', 'C');
        $namespace->setUses([
            'My\Other\Sub\Component',
            ['My\Other\Sub\Component\Foo', 'Foo']
        ]);

        ob_start();
        $namespace->render();
        $result = ob_get_clean();
        $string = (string)$namespace;

        $this->assertContains('namespace MyNamespace', $result);
        $this->assertContains('namespace MyNamespace', $string);
        $this->assertContains('use My\Other\Component as C', $result);
        $this->assertContains('use My\Other\Component as C', $string);
        $this->assertContains('use My\Other\Sub\Component\Foo as Foo', $result);
        $this->assertContains('use My\Other\Sub\Component\Foo as Foo', $string);

    }

}