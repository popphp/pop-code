<?php

namespace Pop\Code\Test\Generator;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class FunctionGeneratorTest extends TestCase
{

    public function testClosure()
    {
        $function = new Generator\FunctionGenerator('foo', true);
        $function->setBody("echo 'Hello World!;");
        $this->assertTrue($function->isClosure());
        $this->assertStringContainsString('};', $function->render());
    }

    public function testUnnamedClosureRendersAsABareAnonymousFunctionExpression()
    {
        // Closure mode previously always required a name (rendering `$name = function(...) {}`)
        // -- but a genuinely anonymous closure (e.g. one reflected with no name override) has no
        // name to assign to at all, and render() unconditionally threw for a null name regardless
        // of closure status.
        $function = new Generator\FunctionGenerator(null, true);
        $function->setBody("echo 'Hello World!';");
        $render = $function->render();

        $this->assertStringContainsString('function()', $render);
        $this->assertStringNotContainsString('= function', $render);
        $this->assertStringEndsWith(';', rtrim($render));
    }

    public function testRenderException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $function = new Generator\FunctionGenerator();
        $function->render();
    }

    public function testAddArgumentsException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $function = new Generator\FunctionGenerator();
        $function->addParameters([
            'name'  => 'bar',
            'value' => 'hello',
            'type'  => 'string'
        ]);
    }

    public function testArguments()
    {
        $function = new Generator\FunctionGenerator('someFunc');
        $function->addParameter('foo', '123', 'int');
        $function->addParameters([
            [
                'name'  => 'bar',
                'value' => 'hello',
                'type'  => 'string'
            ]
        ]);

        $this->assertTrue($function->hasParameters());
        $this->assertTrue($function->hasParameter('foo'));
        $this->assertEquals(2, count($function->getParameters()));
        $this->assertEquals('123', $function->getParameter('foo')['value']);
        $this->assertStringContainsString("function someFunc(int \$foo = 123, string \$bar = 'hello')", (string)$function);
    }

    public function testAddParameterNoValueDefault()
    {
        $function = new Generator\FunctionGenerator('someFunc');
        $function->addParameter('bar');

        $this->assertStringContainsString('function someFunc($bar)', (string)$function);
        $this->assertStringNotContainsString('= null', (string)$function);
    }

    public function testReturnTypes()
    {
        $function = new Generator\FunctionGenerator('someFunc');
        $function->addReturnTypes(['string', 'null']);
        $this->assertTrue($function->hasReturnTypes());
        $this->assertTrue($function->hasReturnType('string'));
        $this->assertCount(2, $function->getReturnTypes());
        $this->assertStringContainsString("): string|null", (string)$function);
    }

    public function testVariadicArgument()
    {
        $function = new Generator\FunctionGenerator('sum');
        $function->addArgument('numbers', new Generator\NoValue(), 'int', true);
        $this->assertStringContainsString('function sum(int ...$numbers)', (string) $function);
    }

    public function testByRefArgument()
    {
        $function = new Generator\FunctionGenerator('increment');
        $function->addArgument('counter', new Generator\NoValue(), 'int', false, true);
        $this->assertStringContainsString('function increment(int &$counter)', (string) $function);
    }

    public function testVariadicAndByRefCombined()
    {
        $function = new Generator\FunctionGenerator('collect');
        $function->addArgument('items', new Generator\NoValue(), null, true, true);
        $this->assertStringContainsString('function collect(&...$items)', (string) $function);
    }

    public function testVariadicWithDefaultValueThrowsException()
    {
        $this->expectException('Pop\Code\Generator\Exception');
        $function = new Generator\FunctionGenerator('bad');
        $function->addArgument('numbers', [1, 2, 3], 'int', true);
    }

    public function testAttributesRenderBeforeFunctionWithNoIndent()
    {
        $function = new Generator\FunctionGenerator('foo');
        $function->addAttribute(new Generator\AttributeGenerator('Pure'));
        $render = (string) $function;

        $this->assertStringContainsString("#[Pure]\nfunction foo", $render);
    }

    public function testParameterAttributeRendersInline()
    {
        $function = new Generator\FunctionGenerator('route');
        $function->addArgument('path', new Generator\NoValue(), 'string', false, false, [
            new Generator\AttributeGenerator('Autowire'),
        ]);
        $this->assertStringContainsString('function route(#[Autowire] string $path)', (string) $function);
    }

    public function testMultipleParameterAttributesAreSpaceSeparated()
    {
        $function = new Generator\FunctionGenerator('route');
        $function->addArgument('path', new Generator\NoValue(), 'string', false, false, [
            new Generator\AttributeGenerator('Autowire'),
            new Generator\AttributeGenerator('Required'),
        ]);
        $this->assertStringContainsString('function route(#[Autowire] #[Required] string $path)', (string) $function);
    }

    public function testTypedArgumentWithNoDefaultDoesNotGainSpuriousNullable()
    {
        // Previously the docblock unconditionally appended |null to any typed parameter's
        // @param tag regardless of whether it actually had a null default -- a parameter with
        // no default at all (NoValue, not null) still got marked nullable in the docblock.
        $function = new Generator\FunctionGenerator('noDefault');
        $function->addArgument('foo', new Generator\NoValue(), 'string');
        $render = (string) $function;

        $this->assertStringContainsString('function noDefault(string $foo)', $render);
        $this->assertStringNotContainsString('string|null $foo', $render);
        $this->assertStringNotContainsString('@param string|null', $render);
    }

    public function testAlreadyNullableTypeWithNullDefaultDoesNotDuplicateNull()
    {
        // Previously an already-nullable type (e.g. reflected from a real `?string`/`string|null`
        // parameter) combined with a null default produced `string|null|null` -- invalid PHP.
        $function = new Generator\FunctionGenerator('alreadyNullable');
        $function->addArgument('bar', null, 'string|null');
        $render = (string) $function;

        $this->assertStringContainsString('string|null $bar = null', $render);
        $this->assertStringNotContainsString('string|null|null', $render);
    }

    public function testReAddingAnArgumentReplacesTheStaleParamDocblockEntry()
    {
        // $this->arguments is name-keyed and correctly overwrites on re-add, but the docblock's
        // params used to be append-only -- re-adding 'foo' with a different type left the old
        // @param entry behind alongside the new one.
        $function = new Generator\FunctionGenerator('test');
        $function->addArgument('foo', null, 'string');
        $function->addArgument('foo', null, 'int');
        $render = (string) $function;

        $this->assertEquals(1, substr_count($render, '@param'));
        $this->assertStringContainsString('int|null  $foo', $render);
        $this->assertStringNotContainsString('string', $render);
    }

    public function testIntersectionTypedParameterWithNoDefaultWrapsInParensBeforeNull()
    {
        // Same DNF-syntax gap as PropertyGenerator: an intersection type combined with a null
        // default needs parens (`(A&B)|null`), not a bare `A&B|null`, which is invalid PHP.
        $function = new Generator\FunctionGenerator('f');
        $function->addArgument('x', null, 'Countable&Traversable');
        $function->setBody('return $x;');
        $render = (string) $function;

        $this->assertStringContainsString('(Countable&Traversable)|null $x = null', $render);
        $this->assertStringContainsString('@param (Countable&Traversable)|null', $render);

        $tmpFile = sys_get_temp_dir() . '/pop-code-intersection-param-' . uniqid() . '.php';
        file_put_contents($tmpFile, "<?php\n" . $render);
        exec('php -l ' . escapeshellarg($tmpFile), $output, $exitCode);
        unlink($tmpFile);
        $this->assertEquals(0, $exitCode, implode("\n", $output));
    }

}