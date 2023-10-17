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
 * Interface generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class InterfaceGenerator extends AbstractClassGenerator
{

    /**
     * Parent interfaces that are extended
     * @var ?string
     */
    protected ?string $parent = null;

    /**
     * Constructor
     *
     * Instantiate the interface generator object
     *
     * @param  string  $name
     * @param  ?string $parent
     */
    public function __construct(string $name, ?string $parent = null)
    {
        $this->setName($name);
        $this->setParent($parent);
    }

    /**
     * Set the interface parent
     *
     * @param  ?string $parent
     * @return InterfaceGenerator
     */
    public function setParent(?string $parent = null): InterfaceGenerator
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the interface parent
     *
     * @return string|null
     */
    public function getParent(): string|null
    {
        return $this->parent;
    }

    /**
     * Has parent
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return ($this->parent !== null);
    }

    /**
     * Render interface
     *
     * @return string
     */
    public function render(): string
    {
        $this->output  = ($this->namespace !== null) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= ($this->docblock !== null) ? $this->docblock->render() : null;
        $this->output .= 'interface ' . $this->name;

        if ($this->parent !== null) {
            $this->output .= ' extends ' . $this->parent;
        }

        $this->output .= PHP_EOL . '{' . PHP_EOL;

        if ($this->hasConstants()) {
            $this->output .= $this->formatConstants();
        }
        if ($this->hasMethods()) {
            $this->output .= $this->formatMethods();
        }

        $this->output .= PHP_EOL . '}' . PHP_EOL;

        return $this->output;
    }

    /**
     * Format the constants
     *
     * @return string
     */
    protected function formatConstants(): string
    {
        $constants = null;

        foreach ($this->constants as $constant) {
            $constants .= $constant->render() . PHP_EOL;
        }

        return $constants;
    }

    /**
     * Format the methods
     *
     * @return string
     */
    protected function formatMethods(): string
    {
        $methods = null;

        foreach ($this->methods as $method) {
            $methods .= $method->render() . PHP_EOL;
        }

        return $methods;
    }

    /**
     * Print interface
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}