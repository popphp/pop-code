<?php
/**
 * This is a docblock
 */
namespace Pop\Code\Test\TestAssets;

/**
 * This is another docblock
 */
trait TestTrait
{

    use AnotherTrait, OneMoreTrait;

    /**
     * @var   string
     */
    public $traitProp = 'str';

    public function baz()
    {
        echo 'Hello World!';
    }

}