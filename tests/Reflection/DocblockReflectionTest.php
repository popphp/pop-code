<?php

namespace Pop\Code\Test\Reflection;

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

    public function testDescriptionOnlyDocblockWithNoTags()
    {
        $doc = <<<DOC
/**
 * Just a description, no tags
 */
DOC;

        $docblock = Reflection\DocblockReflection::parse($doc);
        $this->assertEquals('Just a description, no tags', $docblock->getDesc());
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

    public function testBareParamWithNoTypeIsParsedAsTheVariableNotTheType()
    {
        // Previously a bare "@param $baz" (no type at all) was mis-parsed with the variable name
        // landing in the 'type' slot and 'var' left null -- which meant a later addArgument()
        // call for the same real parameter (which correctly computes the var name) couldn't
        // recognize this as the same param to replace, producing two @param lines for one
        // parameter once rendered.
        $doc = <<<DOC
/**
 * @param \$baz
 */
DOC;

        $docblock = Reflection\DocblockReflection::parse($doc);
        $this->assertTrue($docblock->hasParam(0));
        $this->assertEquals('$baz', $docblock->getParam(0)['var']);
        $this->assertNull($docblock->getParam(0)['type']);
    }

    public function testTypedParamWithDescriptionParsesVarAndDescSeparately()
    {
        // Previously $varName was never truncated after the description was extracted from it,
        // so a typed param with a description (e.g. "@param string $name The name to use" --
        // the shape most real docblocks actually use) stored 'var' as "$name The name to use"
        // instead of just "$name", duplicating the description once rendered.
        $doc = <<<DOC
/**
 * @param string \$name The name to use
 */
DOC;

        $docblock = Reflection\DocblockReflection::parse($doc);
        $param    = $docblock->getParam(0);

        $this->assertEquals('string', $param['type']);
        $this->assertEquals('$name', $param['var']);
        $this->assertEquals('The name to use', $param['desc']);
    }

}