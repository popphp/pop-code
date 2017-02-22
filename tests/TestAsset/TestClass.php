<?php

namespace Pop\Code\Test\TestAsset;

use Pop\Filter\Slug;
use Pop\Filter\ConvertCase as CC;

/**
 * Test class
 *
 * @category   Pop
 * @package    Pop_Test
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class TestClass implements TestInterface
{

    /**
     * Constants
     */
    const HELLO = 'Hello';
    const WORLD = 'World';

    /**
     * Foo var
     * @var string
     */
    public $foo = 'foo';

    /**
     * Bar var
     * @var string
     */
    protected $bar = 'bar';

    /**
     * Baz var
     * @var string
     */
    private $baz = 'baz';

    /**
     * Constructor
     *
     * Instantiate the test class object
     *
     * @param  string $bar
     * @return TestClass
     */
    public function __construct($bar)
    {
        $this->setBar($bar);
    }

    /**
     * Set the bar string
     *
     * @param  string $bar
     * @return TestClass
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
        return $this;
    }

    /**
     * Get the bar string
     *
     * @return string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * Get the baz string
     *
     * @return string
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * Test protected method
     *
     * @return TestClass
     */
    protected function protectedMethod()
    {
        return $this;
    }

    /**
     * Test private method
     *
     * @return TestClass
     */
    protected function privateMethod()
    {
        return $this;
    }

}