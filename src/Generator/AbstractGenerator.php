<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
abstract class AbstractGenerator implements GeneratorInterface
{

    /**
     * Code indent spaces
     * @var int
     */
    protected int $indent = 4;

    /**
     * Output string
     * @var ?string
     */
    protected ?string $output = null;

    /**
     * Set the indent
     *
     * @param  int $indent
     * @return AbstractGenerator
     */
    public function setIndent(int $indent): AbstractGenerator
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Get the indent
     *
     * @return int
     */
    public function getIndent(): int
    {
        return $this->indent;
    }

    /**
     * Has indent
     *
     * @return bool
     */
    public function hasIndent(): bool
    {
        return (!empty($this->indent));
    }

    /**
     * Print the indent
     *
     * @return string
     */
    public function printIndent(): string
    {
        return str_repeat(' ', $this->indent);
    }

    /**
     * Get the output
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Has output
     *
     * @return bool
     */
    public function hasOutput(): bool
    {
        return ($this->output !== null);
    }

    /**
     * Is rendered (alias to hasOutput())
     *
     * @return bool
     */
    public function isRendered(): bool
    {
        return ($this->output !== null);
    }

    /**
     * Render method
     *
     * @return string
     */
    abstract public function render(): string;

}