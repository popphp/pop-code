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
 * Interface generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class InterfaceGenerator extends AbstractClassGenerator
{

    /**
     * Parent interfaces that are extended
     * @var string
     */
    protected $parent = null;

    /**
     * Constructor
     *
     * Instantiate the interface generator object
     *
     * @param  string  $name
     * @param  string  $parent
     */
    public function __construct($name, $parent = null)
    {
        $this->setName($name);
        $this->setParent($parent);
    }

    /**
     * Set the interface parent
     *
     * @param  string $parent
     * @return InterfaceGenerator
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the interface parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Has parent
     *
     * @return boolean
     */
    public function hasParent()
    {
        return (null !== $this->parent);
    }

    /**
     * Render interface
     *
     * @return string
     */
    public function render()
    {
        $this->output  = (null !== $this->namespace) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= (null !== $this->docblock) ? $this->docblock->render() : null;
        $this->output .= 'interface ' . $this->name;

        if (null !== $this->parent) {
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
    protected function formatConstants()
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
    protected function formatMethods()
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
    public function __toString()
    {
        return $this->render();
    }

}