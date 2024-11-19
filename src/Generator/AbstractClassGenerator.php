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
 * Abstract class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
abstract class AbstractClassGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\NamespaceTrait, Traits\DocblockTrait;

    /**
     * Array of constant generator objects
     * @var array
     */
    protected array $constants = [];

    /**
     * Array of method generator objects
     * @var array
     */
    protected array $methods = [];

    /**
     * Add constants
     *
     * @param  array $constants
     * @return AbstractClassGenerator
     */
    public function addConstants(array $constants): AbstractClassGenerator
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
    public function addConstant(ConstantGenerator $constant): AbstractClassGenerator
    {
        $this->constants[$constant->getName()] = $constant;
        return $this;
    }

    /**
     * Get a constant
     *
     * @param  mixed $constant
     * @return ConstantGenerator|null
     */
    public function getConstant(mixed $constant): ConstantGenerator|null
    {
        $c = ($constant instanceof ConstantGenerator) ? $constant->getName() : $constant;
        return (isset($this->constants[$c])) ? $this->constants[$c] : null;
    }

    /**
     * Has a constant
     *
     * @param  mixed $constant
     * @return bool
     */
    public function hasConstant(mixed $constant): bool
    {
        $c = ($constant instanceof ConstantGenerator) ? $constant->getName() : $constant;
        return (isset($this->constants[$c]));
    }

    /**
     * Has constants
     *
     * @return bool
     */
    public function hasConstants(): bool
    {
        return (!empty($this->constants));
    }

    /**
     * Get all constants
     *
     * @return array
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Remove a constant
     *
     * @param  mixed $constant
     * @return AbstractClassGenerator
     */
    public function removeConstant(mixed $constant): AbstractClassGenerator
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
    public function addMethods(array $methods): AbstractClassGenerator
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
    public function addMethod(MethodGenerator $method): AbstractClassGenerator
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }

    /**
     * Get a method
     *
     * @param  mixed $method
     * @return MethodGenerator|null
     */
    public function getMethod(mixed $method): MethodGenerator|null
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        return (isset($this->methods[$m])) ? $this->methods[$m] : null;
    }

    /**
     * Has a method
     *
     * @param  mixed $method
     * @return bool
     */
    public function hasMethod(mixed $method): bool
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        return (isset($this->methods[$m]));
    }

    /**
     * Has methods
     *
     * @return bool
     */
    public function hasMethods(): bool
    {
        return (!empty($this->methods));
    }

    /**
     * Get all methods
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Remove a method
     *
     * @param  mixed $method
     * @return AbstractClassGenerator
     */
    public function removeMethod(mixed $method): AbstractClassGenerator
    {
        $m = ($method instanceof MethodGenerator) ? $method->getName() : $method;
        if (isset($this->methods[$m])) {
            unset($this->methods[$m]);
        }
        return $this;
    }

}
