<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator\Literal;
use PHPUnit\Framework\TestCase;

class LiteralTest extends TestCase
{

    public function testWrapsRawValue()
    {
        $literal = new Literal('self::FOO');
        $this->assertEquals('self::FOO', $literal->getValue());
    }

}
