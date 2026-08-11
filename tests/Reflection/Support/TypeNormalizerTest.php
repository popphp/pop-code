<?php

namespace Pop\Code\Test\Reflection\Support;

use Pop\Code\Reflection\Support\TypeNormalizer;
use PHPUnit\Framework\TestCase;

class TypeNormalizerTest extends TestCase
{

    public function testNormalizesLegacyGettypeNames()
    {
        $this->assertEquals('int', TypeNormalizer::normalize('integer'));
        $this->assertEquals('bool', TypeNormalizer::normalize('boolean'));
        $this->assertEquals('float', TypeNormalizer::normalize('double'));
        $this->assertEquals('null', TypeNormalizer::normalize('NULL'));
    }

    public function testPassesThroughAlreadyCorrectNames()
    {
        $this->assertEquals('string', TypeNormalizer::normalize('string'));
        $this->assertEquals('array', TypeNormalizer::normalize('array'));
        $this->assertEquals('object', TypeNormalizer::normalize('object'));
    }

}
