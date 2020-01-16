<?php

namespace Pop\Code\Test;

use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class DocblockReflectionTest extends TestCase
{

    public function testException()
    {
        $this->expectException('Pop\Code\Reflection\Exception');
        $docblock = Reflection\DocblockReflection::parse('bad docblock');
    }

    public function testParamDesc()
    {
        $doc = <<<DOC
/**
 * @param string \$str This is a string
 *
 */
DOC;

        $docblock = Reflection\DocblockReflection::parse($doc);
        $this->assertTrue($docblock->hasParam(0));
        $this->assertEquals('This is a string', $docblock->getParam(0)['desc']);
    }

    public function testReturn()
    {
        $doc = <<<DOC
/**
 * @param string \$str This is a string
 * @return array
 */
DOC;

        $docblock = Reflection\DocblockReflection::parse($doc);
        $this->assertTrue($docblock->hasReturn());
        $this->assertEquals('array', $docblock->getReturn()['type']);
    }

}