<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator;

/**
 * Function generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class FunctionGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\FunctionTrait, Traits\BodyTrait;

    /**
     * Function interface flag
     * @var boolean
     */
    protected $closure = false;

    /**
     * Function body
     * @var string
     */
    protected $body = null;

    /**
     * Function indent
     * @var int
     */
    protected $indent = 0;

    /**
     * Constructor
     *
     * Instantiate the function generator object
     *
     * @param  string  $name
     * @param  boolean $closure
     */
    public function __construct($name = null, $closure = false)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        $this->setAsClosure($closure);
    }

    /**
     * Set the function closure flag
     *
     * @param  boolean $closure
     * @return FunctionGenerator
     */
    public function setAsClosure($closure = false)
    {
        $this->closure = (boolean)$closure;
        return $this;
    }

    /**
     * Get the function closure flag
     *
     * @return boolean
     */
    public function isClosure()
    {
        return $this->closure;
    }

    /**
     * Render function
     *
     * @throws Exception
     * @return string
     */
    public function render()
    {
        if (null === $this->name) {
            throw new Exception('Error: The function name has not been set.');
        }

        $args = $this->formatArguments();

        $this->output = PHP_EOL . ((null !== $this->docblock) ? $this->docblock->render() : null);
        if ($this->closure) {
            $this->output .= $this->printIndent() . '$' . $this->name .' = function(' . $args . ')';
        } else {
            $this->output .= $this->printIndent() . 'function ' . $this->name . '(' . $args . ')';
        }

        $this->output .= PHP_EOL . $this->printIndent() . '{' . PHP_EOL;
        $this->output .= $this->body. PHP_EOL;
        $this->output .= $this->printIndent() . '}';

        if ($this->closure) {
            $this->output .= ';';
        }

        $this->output .= PHP_EOL;

        return $this->output;
    }

    /**
     * Print function
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}