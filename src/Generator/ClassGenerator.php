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
 * Class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class ClassGenerator extends AbstractClassGenerator
{

    use Traits\PropertiesTrait, Traits\AbstractFinalTrait;

    /**
     * Parent class that is extended
     * @var string
     */
    protected $parent = null;

    /**
     * Interface that is implemented
     * @var string
     */
    protected $interface = null;

    /**
     * Constructor
     *
     * Instantiate the class generator object
     *
     * @param  string  $name
     * @param  string  $parent
     * @param  string  $interface
     * @param  boolean $abstract
     */
    public function __construct($name, $parent = null, $interface = null, $abstract = false)
    {
        $this->setName($name);
        if (null !== $parent) {
            $this->setParent($parent);
        }
        if (null !== $interface) {
            $this->setInterface($interface);
        }
        $this->setAsAbstract($abstract);
    }

    /**
     * Set the class parent
     *
     * @param  string $parent
     * @return ClassGenerator
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the class parent
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
     * Set the class interface
     *
     * @param  string $interface
     * @return ClassGenerator
     */
    public function setInterface($interface = null)
    {
        $this->interface = $interface;
        return $this;
    }

    /**
     * Get the class interface
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * Has class interface
     *
     * @return boolean
     */
    public function hasInterface()
    {
        return (null !== $this->interface);
    }

    /**
     * Render class
     *
     * @return string
     */
    public function render()
    {
        $classKeyword = null;

        if ($this->isAbstract()) {
            $classKeyword = 'abstract ';
        } else if ($this->isFinal()) {
            $classKeyword = 'final ';
        }

        $this->output  = (null !== $this->namespace) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= (null !== $this->docblock) ? $this->docblock->render() : null;
        $this->output .= $classKeyword . 'class ' . $this->name;

        if (null !== $this->parent) {
            $this->output .= ' extends ' . $this->parent;
        }
        if (null !== $this->interface) {
            $this->output .= ' implements ' . $this->interface;
        }

        $this->output .= PHP_EOL . '{';
        $this->output .= $this->formatProperties() . PHP_EOL;
        $this->output .= $this->formatMethods() . PHP_EOL;
        $this->output .= '}' . PHP_EOL;

        return $this->output;
    }

    /**
     * Format the properties
     *
     * @return string
     */
    protected function formatProperties()
    {
        $props = null;

        foreach ($this->properties as $prop) {
            $props .= PHP_EOL . $prop->render();
        }

        return $props;
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
            $methods .= PHP_EOL . $method->render();
        }

        return $methods;
    }

    /**
     * Print class
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}