<?php
/**
 * This is a docblock
 */
namespace Pop\Code\Test\TestAssets;

/**
 * This is another docblock
 */
class TestClass extends AbstractTestClass
{

    use TestTrait;

    /**
     * @var   string
     */
    const SOME_STR = 'value';

    /**
     * @var   number
     */
    const SOME_NUM = '123';

    /**
     * @var   string
     */
    public $myProp = null;

    /**
     * @var   array
     */
    public $otherProp = [];

    protected $testProp1 = 'str';

    /**
     * @var   string
     */
    private $testProp2 = null;

    /**
     * @param $baz
     */
    public function bar($baz)
    {
        echo $baz;
        echo 'something else 123';
    }

    /**
     * @param $str
     */
    public function printSomething($str)
    {
        echo $str;
    }

    /**
     * @param $str
     */
    protected function test1($str)
    {
        echo $str;
    }

    /**
     * @param $str
     */
    private function test2($str)
    {
        echo $str;
    }

}
