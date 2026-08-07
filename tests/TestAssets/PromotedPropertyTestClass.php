<?php
/**
 * Fixture: constructor property promotion mixed with an ordinary no-default typed property.
 */
namespace Pop\Code\Test\TestAssets;

class PromotedPropertyTestClass
{

    public string $noDefault;

    public function __construct(protected int $x = 1)
    {
    }

}
