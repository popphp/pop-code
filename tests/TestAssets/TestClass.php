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
     * @param ?string $str
     * @return string|null
     */
    protected function test1(?string $str = null): string|null
    {
        return $str;
    }

    /**
     * @param ?string $str
     * @return array|string|null
     */
    private function test2(?string $str = null): array|string|null
    {
        return $str;
    }

}
