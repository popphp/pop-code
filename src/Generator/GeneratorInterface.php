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
 * Generator interface
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
interface GeneratorInterface
{

    /**
     * Set the indent
     *
     * @param  int $indent
     * @return GeneratorInterface
     */
    public function setIndent($indent);

    /**
     * Get the indent
     *
     * @return int
     */
    public function getIndent();

    /**
     * Has indent
     *
     * @return boolean
     */
    public function hasIndent();

    /**
     * Print the indent
     *
     * @return string
     */
    public function printIndent();

    /**
     * Get the output
     *
     * @return string
     */
    public function getOutput();

    /**
     * Has output
     *
     * @return boolean
     */
    public function hasOutput();

    /**
     * Is rendered (alias to hasOutput())
     *
     * @return boolean
     */
    public function isRendered();

    /**
     * Render method
     *
     * @return string
     */
    public function render();

}