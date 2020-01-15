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

    use Traits\UseTrait, Traits\PropertiesTrait, Traits\AbstractFinalTrait;

    /**
     * Parent class that is extended
     * @var string
     */
    protected $parent = null;

    /**
     * Interfaces that are implemented
     * @var array
     */
    protected $interfaces = [];

    /**
     * Constructor
     *
     * Instantiate the class generator object
     *
     * @param  string  $name
     * @param  string  $parent
     * @param  mixed   $interface
     * @param  boolean $abstract
     */
    public function __construct($name, $parent = null, $interface = null, $abstract = false)
    {
        $this->setName($name);

        if (null !== $parent) {
            $this->setParent($parent);
        }

        if (null !== $interface) {
            if (is_array($interface)) {
                $this->addInterfaces($interface);
            } else if (strpos($interface, ',') !== false) {
                $this->addInterfaces(array_map('trim', explode(',', $interface)));
            } else {
                $this->addInterface($interface);
            }
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
     * Add a class interface
     *
     * @param  string $interface
     * @return ClassGenerator
     */
    public function addInterface($interface)
    {
        if (!in_array($interface, $this->interfaces)) {
            $this->interfaces[] = $interface;
        }

        return $this;
    }

    /**
     * Add a class interface
     *
     * @param  array $interfaces
     * @return ClassGenerator
     */
    public function addInterfaces(array $interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }

        return $this;
    }

    /**
     * Get the class interfaces
     *
     * @return array
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Has class interfaces
     *
     * @return boolean
     */
    public function hasInterfaces()
    {
        return (!empty($this->interfaces));
    }

    /**
     * Has class interface
     *
     * @param  string $interface
     * @return boolean
     */
    public function hasInterface($interface)
    {
        return (in_array($interface, $this->interfaces));
    }

    /**
     * Remove class interface
     *
     * @param  string $interface
     * @return ClassGenerator
     */
    public function removeInterface($interface)
    {
        if (in_array($interface, $this->interfaces)) {
            $key = array_search($interface, $this->interfaces);
            unset($this->interfaces[$key]);
        }

        return $this;
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
        if (!empty($this->interfaces)) {
            $this->output .= ' implements ' . implode(', ', $this->interfaces);
        }

        $this->output .= PHP_EOL . '{' . PHP_EOL;

        if ($this->hasUses()) {
            $this->output .= PHP_EOL;
            foreach ($this->uses as $ns => $as) {
                $this->output .= $this->printIndent() . 'use ';
                $this->output .= $ns;
                if (null !== $as) {
                    $this->output .= ' as ' . $as;
                }
                $this->output .= ';' . PHP_EOL;
            }
        }

        if ($this->hasConstants()) {
            $this->output .= $this->formatConstants();
        }
        if ($this->hasProperties()) {
            $this->output .= $this->formatProperties();
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
     * Format the properties
     *
     * @return string
     */
    protected function formatProperties()
    {
        $props = null;

        foreach ($this->properties as $prop) {
            $props .= $prop->render() . PHP_EOL;
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
            $methods .= $method->render() . PHP_EOL;
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