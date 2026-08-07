<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class EnumGeneratorTest extends TestCase
{

    public function testConstructor()
    {
        $enum = new Generator\EnumGenerator('Status', 'string', 'Colorful');
        $this->assertInstanceOf('Pop\Code\Generator\EnumGenerator', $enum);
        $this->assertTrue($enum->hasBackingType());
        $this->assertEquals('string', $enum->getBackingType());
        $this->assertTrue($enum->hasInterface('Colorful'));
    }

    public function testCases()
    {
        $enum = new Generator\EnumGenerator('Status', 'string');
        $enum->addCases([
            new Generator\EnumCaseGenerator('Active', 'active'),
            new Generator\EnumCaseGenerator('Inactive', 'inactive'),
        ]);

        $this->assertTrue($enum->hasCases());
        $this->assertTrue($enum->hasCase('Active'));
        $this->assertEquals(2, count($enum->getCases()));
        $this->assertInstanceOf('Pop\Code\Generator\EnumCaseGenerator', $enum->getCase('Active'));

        $enum->removeCase('Active');
        $this->assertFalse($enum->hasCase('Active'));
    }

    public function testRenderBackedEnum()
    {
        $enum = new Generator\EnumGenerator('Status', 'string');
        $enum->addCase(new Generator\EnumCaseGenerator('Active', 'active'));
        $render = (string) $enum;

        $this->assertStringContainsString('enum Status: string', $render);
        $this->assertStringContainsString("case Active = 'active';", $render);
    }

    public function testRenderPureEnum()
    {
        $enum = new Generator\EnumGenerator('Suit');
        $enum->addCase(new Generator\EnumCaseGenerator('Hearts'));
        $render = (string) $enum;

        $this->assertStringContainsString('enum Suit', $render);
        $this->assertStringNotContainsString('enum Suit:', $render);
        $this->assertStringContainsString('case Hearts;', $render);
    }

    public function testRenderWithInterfacesUsesAndMembers()
    {
        $enum = new Generator\EnumGenerator('Status', 'string', 'Colorful');
        $enum->addUse('StatusHelpers');
        $enum->addCase(new Generator\EnumCaseGenerator('Active', 'active'));
        $enum->addConstant(new Generator\ConstantGenerator('DEFAULT', 'string', 'active'));
        $enum->addMethod(new Generator\MethodGenerator('label'));
        $render = (string) $enum;

        $this->assertStringContainsString('enum Status: string implements Colorful', $render);
        $this->assertStringContainsString('use StatusHelpers;', $render);
        $this->assertStringContainsString("case Active = 'active';", $render);
        $this->assertStringContainsString('const DEFAULT', $render);
        $this->assertStringContainsString('function label', $render);
    }

    public function testAddMethodRejectsConstructor()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $enum = new Generator\EnumGenerator('Status', 'string');
        $enum->addMethod(new Generator\MethodGenerator('__construct'));
    }

    public function testRenderThrowsWhenBackedCaseHasNoValue()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $enum = new Generator\EnumGenerator('Status', 'string');
        $enum->addCase(new Generator\EnumCaseGenerator('Active'));
        $enum->render();
    }

    public function testRenderThrowsWhenNonBackedCaseHasValue()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $enum = new Generator\EnumGenerator('Suit');
        $enum->addCase(new Generator\EnumCaseGenerator('Hearts', 'hearts'));
        $enum->render();
    }

}
