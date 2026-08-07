<?php
/**
 * Fixture: attributes on a class (including a repeated, same-namespace attribute and a
 * foreign-namespace one), a property, a typed constant, a method, and a parameter.
 */
namespace Pop\Code\Test\TestAssets;

use Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute;

#[TagAttribute('one')]
#[TagAttribute(name: 'two', priority: 5)]
#[ForeignTagAttribute('class-level')]
class AttributedTestClass
{

    #[TagAttribute('prop')]
    #[ForeignTagAttribute('member-level')]
    public string $label = 'x';

    #[TagAttribute('const')]
    public const int LIMIT = 10;

    #[TagAttribute('method')]
    public function greet(#[TagAttribute('param')] string $name): string
    {
        return $name;
    }

}
