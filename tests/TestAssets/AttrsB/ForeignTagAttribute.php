<?php
/**
 * Fixture: an attribute class with the SAME short name (ForeignTagAttribute) as
 * Pop\Code\Test\TestAssets\Attrs\ForeignTagAttribute, but in a different namespace -- used to
 * exercise the same-short-name/different-namespace `use`-import collision fallback.
 */
namespace Pop\Code\Test\TestAssets\AttrsB;

use Attribute;

#[Attribute]
class ForeignTagAttribute
{

    public function __construct(public string $label = '')
    {
    }

}
