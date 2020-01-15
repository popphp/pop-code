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
namespace Pop\Code;

/**
 * Reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class Reflection
{

    /**
     * Create class
     *
     * @param  mixed  $class
     * @param  string $name
     * @return Generator\ClassGenerator
     */
    public static function createClass($class, $name = null)
    {
        return Reflection\ClassReflection::parse($class, $name);
    }

    /**
     * Create trait
     *
     * @param  mixed  $trait
     * @param  string $name
     * @return Generator\TraitGenerator
     */
    public static function createTrait($trait, $name = null)
    {
        return Reflection\TraitReflection::parse($trait, $name);
    }

    /**
     * Create interface
     *
     * @param  mixed  $interface
     * @param  string $name
     * @return Generator\InterfaceGenerator
     */
    public static function createInterface($interface, $name = null)
    {
        return Reflection\InterfaceReflection::parse($interface, $name);
    }

    /**
     * Create namespace
     *
     * @param  mixed  $namespace
     * @param  string $name
     * @return Generator\NamespaceGenerator
     */
    public static function createNamespace($namespace, $name = null)
    {
        return Reflection\NamespaceReflection::parse($namespace, $name);
    }

    /**
     * Create docblock
     *
     * @param  mixed  $docblock
     * @param  int    $forceIndent
     * @return Generator\DocblockGenerator
     */
    public static function createDocblock($docblock, $forceIndent = null)
    {
        return Reflection\DocblockReflection::parse($docblock, $forceIndent);
    }

    /**
     * Create function
     *
     * @param  mixed  $function
     * @param  string $name
     * @return Generator\FunctionGenerator
     */
    public static function createFunction($function, $name = null)
    {
        return Reflection\FunctionReflection::parse($function, $name);
    }

    /**
     * Create method
     *
     * @param  mixed  $method
     * @param  string $name
     * @return Generator\MethodGenerator
     */
    public static function createMethod($method, $name = null)
    {
        return Reflection\MethodReflection::parse($method, $name);
    }

    /**
     * Create property
     *
     * @param  mixed  $property
     * @param  string $name
     * @param  mixed  $value
     * @return Generator\PropertyGenerator
     */
    public static function createProperty($property, $name = null, $value = null)
    {
        return Reflection\PropertyReflection::parse($property, $name, $value);
    }

}