<?php
/**
 * Fixture: a readonly class, where every property is implicitly readonly.
 */
namespace Pop\Code\Test\TestAssets;

readonly class ReadonlyClassTestClass
{

    public int $id;
    protected string $label;

    public function __construct(int $id, string $label)
    {
        $this->id    = $id;
        $this->label = $label;
    }

}
