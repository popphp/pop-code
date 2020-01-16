<?php

namespace Pop\Code\Test\TestAsset;

class Test
{

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

}
