<?php
/**
 * Fixture: an enum-level attribute and a case-level attribute sharing the same short name
 * (ForeignTagAttribute) from two different foreign namespaces -- guards the same collision
 * fallback as CollidingAttributesTestClass, but for EnumReflection's enum-level/case-level shared
 * resolver.
 */
namespace Pop\Code\Test\TestAssets;

use Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute;
use Pop\Code\Test\TestAssets\AttrsB\ForeignTagAttribute as ForeignTagAttributeB;

#[ForeignTagAttribute('enum-level')]
enum CollidingAttributesEnum: string
{

    #[ForeignTagAttributeB('case-level')]
    case Active = 'active';

}
