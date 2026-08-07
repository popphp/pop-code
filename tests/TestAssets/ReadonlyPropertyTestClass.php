<?php
/**
 * Fixture: an ordinary (non-readonly) class with one explicit readonly property.
 */
namespace Pop\Code\Test\TestAssets;

class ReadonlyPropertyTestClass
{

    public readonly string $token;
    public string $mutable;

    public function __construct(string $token, string $mutable)
    {
        $this->token   = $token;
        $this->mutable = $mutable;
    }

}
