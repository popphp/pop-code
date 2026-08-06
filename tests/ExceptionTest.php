<?php

namespace Pop\Code\Test;

use Pop\Code\Exception;
use Pop\Code\Generator;
use Pop\Code\Reflection;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{

    public function testGeneratorExceptionIsCatchableAsBaseException()
    {
        $caught = false;

        try {
            throw new Generator\Exception('boom');
        } catch (Exception $e) {
            $caught = true;
        }

        $this->assertTrue($caught);
    }

    public function testReflectionExceptionIsCatchableAsBaseException()
    {
        $caught = false;

        try {
            throw new Reflection\Exception('boom');
        } catch (Exception $e) {
            $caught = true;
        }

        $this->assertTrue($caught);
    }

    public function testTraitsExceptionIsCatchableAsBaseException()
    {
        $caught = false;

        try {
            throw new Generator\Traits\Exception('boom');
        } catch (Exception $e) {
            $caught = true;
        }

        $this->assertTrue($caught);
    }

}
