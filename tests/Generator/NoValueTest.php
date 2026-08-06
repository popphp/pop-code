<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator\NoValue;
use PHPUnit\Framework\TestCase;

class NoValueTest extends TestCase
{

    public function testIsDistinctFromNull()
    {
        $noValue = new NoValue();
        $this->assertInstanceOf(NoValue::class, $noValue);
        $this->assertNotNull($noValue);
    }

}
