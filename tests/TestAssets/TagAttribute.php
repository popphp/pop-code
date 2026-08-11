<?php
/**
 * Fixture: a repeatable attribute accepting both positional and named arguments.
 */
namespace Pop\Code\Test\TestAssets;

use Attribute;

#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class TagAttribute
{

    public function __construct(public string $name, public int $priority = 0)
    {
    }

}
