<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class TraitReflectionTest extends TestCase
{

    public function testException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $trait = Reflection\TraitReflection::parse('Pop\Code\Test\TestAssets\TestClass');
    }

}