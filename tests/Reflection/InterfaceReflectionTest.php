<?php

namespace Pop\Code\Test\Reflection;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class InterfaceReflectionTest extends TestCase
{

    public function testInterfaceException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $interface = Reflection\InterfaceReflection::parse('Pop\Code\Test\TestAssets\TestClass');
    }

}