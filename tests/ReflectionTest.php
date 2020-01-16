<?php

namespace Pop\Code\Test;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ReflectionTest extends TestCase
{

    public function testConstructor()
    {
        $class = Reflection::createClass('Pop\Code\Test\TestAsset\Test');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

}