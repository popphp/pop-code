<?php
/**
 * This is a docblock
 */
namespace Pop\Code\Test\TestAssets;

/**
 * This is another docblock
 */
abstract class AbstractTestClass implements TestInterface
{

    /**
     * @param $baz
     */
    abstract public function bar($baz);

    /**
     * @param $str
     */
    abstract public function printSomething($str);

}
