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
 * Trait generator code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.2.0
 */
class TraitGenerator implements GeneratorInterface
{

    /**
     * Docblock generator object
     * @var DocblockGenerator
     */
    protected $docblock = null;

    /**
     * Namespace generator object
     * @var NamespaceGenerator
     */
    protected $namespace = null;

    /**
     * Trait name
     * @var string
     */
    protected $name = null;

    /**
     * Array of property generator objects
     * @var array
     */
    protected $properties = [];

    /**
     * Array of method generator objects
     * @var array
     */
    protected $methods = [];

    /**
     * Trait indent
     * @var string
     */
    protected $indent = null;

    /**
     * Class output
     * @var string
     */
    protected $output = null;

    /**
     * Constructor
     *
     * Instantiate the trait generator object
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Set the trait indent
     *
     * @param  string $indent
     * @return TraitGenerator
     */
    public function setIndent($indent = null)
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Get the trait indent
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Set the trait name
     *
     * @param  string $name
     * @return TraitGenerator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the trait name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the namespace generator object
     *
     * @param  NamespaceGenerator $namespace
     * @return TraitGenerator
     */
    public function setNamespace(NamespaceGenerator $namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Access the namespace generator object
     *
     * @return NamespaceGenerator
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set the docblock generator object
     *
     * @param  DocblockGenerator $docblock
     * @return TraitGenerator
     */
    public function setDocblock(DocblockGenerator $docblock)
    {
        $this->docblock = $docblock;
        return $this;
    }

    /**
     * Access the docblock generator object
     *
     * @return DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    /**
     * Add a trait property
     *
     * @param  PropertyGenerator $property
     * @return TraitGenerator
     */
    public function addProperty(PropertyGenerator $property)
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * Get a trait property
     *
     * @param  mixed $property
     * @return PropertyGenerator
     */
    public function getProperty($property)
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        return (isset($this->properties[$p])) ? $this->properties[$p] : null;
    }

    /**
     * Get all properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Remove a trait property
     *
     * @param  mixed $property
     * @return TraitGenerator
     */
    public function removeProperty($property)
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        if (isset($this->properties[$p])) {
            unset($this->properties[$p]);
        }
        return $this;
    }

    /**
     * Add a trait method
     *
     * @param  MethodGenerator $method
     * @return TraitGenerator
     */
    public function addMethod(MethodGenerator $method)
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }

    /**
     * Get a method property
     *
     * @param  mixed $method
     * @return MethodGenerator
     */
    public function getMethod($method)
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        return (isset($this->methods[$m])) ? $this->methods[$m] : null;
    }

    /**
     * Get all methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Remove a method property
     *
     * @param  mixed $method
     * @return TraitGenerator
     */
    public function removeMethod($method)
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        if (isset($this->methods[$m])) {
            unset($this->methods[$m]);
        }
        return $this;
    }

    /**
     * Render trait
     *
     * @param  boolean $ret
     * @return mixed
     */
    public function render($ret = false)
    {
        $this->output = (null !== $this->namespace) ? $this->namespace->render(true) . PHP_EOL : null;
        $this->output .= (null !== $this->docblock) ? $this->docblock->render(true) : null;
        $this->output .= 'trait ' . $this->name;

        $this->output .= PHP_EOL . '{';
        $this->output .= $this->formatProperties() . PHP_EOL;
        $this->output .= $this->formatMethods() . PHP_EOL;
        $this->output .= '}' . PHP_EOL;

        if ($ret) {
            return $this->output;
        } else {
            echo $this->output;
        }
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
            $props .= PHP_EOL . $prop->render(true);
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
            $methods .= PHP_EOL . $method->render(true);
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
        return $this->render(true);
    }

}
