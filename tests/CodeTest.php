<?php

namespace Pop\Code\Test;

use Pop\Code;

class CodeTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $code = new Code\Generator('foo.php');
        $this->assertInstanceOf('Pop\Code\Generator', $code);
    }

}