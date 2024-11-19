<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
interface GeneratorInterface
{

    /**
     * Set the indent
     *
     * @param  int $indent
     * @return GeneratorInterface
     */
    public function setIndent(int $indent): GeneratorInterface;

    /**
     * Get the indent
     *
     * @return int
     */
    public function getIndent(): int;

    /**
     * Has indent
     *
     * @return bool
     */
    public function hasIndent(): bool;

    /**
     * Print the indent
     *
     * @return string
     */
    public function printIndent(): string;

    /**
     * Get the output
     *
     * @return string
     */
    public function getOutput(): string;

    /**
     * Has output
     *
     * @return bool
     */
    public function hasOutput(): bool;

    /**
     * Is rendered (alias to hasOutput())
     *
     * @return bool
     */
    public function isRendered(): bool;

    /**
     * Render method
     *
     * @return string
     */
    public function render(): string;

}
