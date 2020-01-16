<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class ClassGeneratorTest extends TestCase
{

    public function testConstructor()
    {
        $class = new Generator\ClassGenerator('Foo');
        $this->assertInstanceOf('Pop\Code\Generator\ClassGenerator', $class);
    }

}