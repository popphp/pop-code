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
 * Abstract generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
abstract class AbstractGenerator implements GeneratorInterface
{

    /**
     * Code indent spaces
     * @var int
     */
    protected $indent = 4;

    /**
     * Output string
     * @var string
     */
    protected $output = null;

    /**
     * Set the indent
     *
     * @param  string $indent
     * @return AbstractGenerator
     */
    public function setIndent($indent)
    {
        $this->indent = (int)$indent;
        return $this;
    }

    /**
     * Get the indent
     *
     * @return int
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Print the indent
     *
     * @return string
     */
    public function printIndent()
    {
        return str_repeat(' ', $this->indent);
    }

    /**
     * Get the output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Render method
     *
     * @return string
     */
    abstract public function render();

}