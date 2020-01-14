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
 * Abstract class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
abstract class AbstractClassGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait;

    /**
     * Namespace generator object
     * @var NamespaceGenerator
     */
    protected $namespace = null;

    /**
     * Array of method generator objects
     * @var array
     */
    protected $methods = [];

    /**
     * Set the namespace generator object
     *
     * @param  NamespaceGenerator $namespace
     * @return AbstractClassGenerator
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
     * Has a namespace generator object
     *
     * @return boolean
     */
    public function hasNamespace()
    {
        return (null !== $this->namespace);
    }

    /**
     * Add a method
     *
     * @param  MethodGenerator $method
     * @return AbstractClassGenerator
     */
    public function addMethod(MethodGenerator $method)
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }

    /**
     * Get a method
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
     * Has a method
     *
     * @param  mixed $method
     * @return boolean
     */
    public function hasMethod($method)
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        return (isset($this->methods[$m]));
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
     * Remove a method
     *
     * @param  mixed $method
     * @return AbstractClassGenerator
     */
    public function removeMethod($method)
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        if (isset($this->methods[$m])) {
            unset($this->methods[$m]);
        }
        return $this;
    }

}