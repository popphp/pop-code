<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class NamespaceReflectionTest extends TestCase
{

    public function testNameException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $namespace = Reflection\NamespaceReflection::parse('bad');
    }


    public function testAlias()
    {
        $name = <<<NAME
namespace Foo;

use Test as T;
NAME;

        $namespace = Reflection\NamespaceReflection::parse($name);
        $this->assertTrue($namespace->hasUses());
    }

}