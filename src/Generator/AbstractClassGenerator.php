<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
abstract class AbstractClassGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\NamespaceTrait, Traits\DocblockTrait;

    /**
     * Array of constant generator objects
     * @var array
     */
    protected $constants = [];

    /**
     * Array of method generator objects
     * @var array
     */
    protected $methods = [];

    /**
     * Add constants
     *
     * @param  array $constants
     * @return AbstractClassGenerator
     */
    public function addConstants(array $constants)
    {
        foreach ($constants as $constant) {
            $this->addConstant($constant);
        }
        return $this;
    }

    /**
     * Add a constant
     *
     * @param  ConstantGenerator $constant
     * @return AbstractClassGenerator
     */
    public function addConstant(ConstantGenerator $constant)
    {
        $this->constants[$constant->getName()] = $constant;
        return $this;
    }

    /**
     * Get a constant
     *
     * @param  mixed $constant
     * @return ConstantGenerator
     */
    public function getConstant($constant)
    {
        $c = ($constant instanceof ConstantGenerator) ? $constant->getName() : $constant;
        return (isset($this->constants[$c])) ? $this->constants[$c] : null;
    }

    /**
     * Has a constant
     *
     * @param  mixed $constant
     * @return boolean
     */
    public function hasConstant($constant)
    {
        $c = ($constant instanceof ConstantGenerator) ? $constant->getName() : $constant;
        return (isset($this->constants[$c]));
    }

    /**
     * Has constants
     *
     * @return boolean
     */
    public function hasConstants()
    {
        return (!empty($this->constants));
    }

    /**
     * Get all constants
     *
     * @return array
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Remove a constant
     *
     * @param  mixed $constant
     * @return AbstractClassGenerator
     */
    public function removeConstant($constant)
    {
        $c = ($constant instanceof ConstantGenerator) ? $constant->getName() : $constant;
        if (isset($this->constants[$c])) {
            unset($this->constants[$c]);
        }
        return $this;
    }

    /**
     * Add methods
     *
     * @param  array $methods
     * @return AbstractClassGenerator
     */
    public function addMethods(array $methods)
    {
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
        return $this;
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
     * Has methods
     *
     * @return boolean
     */
    public function hasMethods()
    {
        return (!empty($this->methods));
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