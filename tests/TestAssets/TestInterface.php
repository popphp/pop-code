<?php
/**
 * This is a docblock
 */
namespace Pop\Code\Test\TestAssets;

/**
 * This is another docblock
 */
interface TestInterface extends ParentInterface
{

    const FOO = 'FOO';

    /**
     * @param $baz
     */
    public function bar($baz);

    /**
     * @param $str
     */
    public function printSomething($str);

}