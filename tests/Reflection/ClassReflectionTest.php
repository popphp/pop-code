<?php

namespace Pop\Code\Test;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ClassReflectionTest extends TestCase
{

    public function testConstructor()
    {
        $class = Reflection\ClassReflection::parse('Pop\Code\Test\TestAsset\Test');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

}