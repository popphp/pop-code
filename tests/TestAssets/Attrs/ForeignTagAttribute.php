<?php
/**
 * Fixture: an attribute in a different namespace from the classes that use it.
 */
namespace Pop\Code\Test\TestAssets\Attrs;

use Attribute;

#[Attribute]
class ForeignTagAttribute
{

    public function __construct(public string $label = '')
    {
    }

}
