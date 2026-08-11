<?php
/**
 * Fixture: two attributes with the identical short name (ForeignTagAttribute) from two different
 * foreign namespaces -- regenerating this without collision handling would emit two conflicting
 * `use` statements ("Cannot use X as Y because the name is already in use").
 */
namespace Pop\Code\Test\TestAssets;

use Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute;
use Pop\Code\Test\TestAssets\AttrsB\ForeignTagAttribute as ForeignTagAttributeB;

#[ForeignTagAttribute('first')]
#[ForeignTagAttributeB('second')]
class CollidingAttributesTestClass
{

}
