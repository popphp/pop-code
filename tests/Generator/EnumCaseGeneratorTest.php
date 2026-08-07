<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class EnumCaseGeneratorTest extends TestCase
{

    public function testBareCaseRenders()
    {
        $case = new Generator\EnumCaseGenerator('Active');
        $this->assertEquals('Active', $case->getName());
        $this->assertFalse($case->hasValue());
        $this->assertStringContainsString('case Active;', (string) $case);
    }

    public function testStringBackedCaseRenders()
    {
        $case = new Generator\EnumCaseGenerator('Active', 'active');
        $this->assertTrue($case->hasValue());
        $this->assertEquals('active', $case->getValue());
        $this->assertStringContainsString("case Active = 'active';", (string) $case);
    }

    public function testIntBackedCaseRenders()
    {
        $case = new Generator\EnumCaseGenerator('One', 1);
        $this->assertStringContainsString('case One = 1;', (string) $case);
    }

    public function testCaseWithDescriptionRendersDocblock()
    {
        $case = new Generator\EnumCaseGenerator('Active', 'active');
        $case->setDesc('The active status');
        $render = (string) $case;

        $this->assertStringContainsString('/**', $render);
        $this->assertStringContainsString('The active status', $render);
        $this->assertStringContainsString("case Active = 'active';", $render);
    }

}
