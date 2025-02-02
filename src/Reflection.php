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
namespace Pop\Code;

use Pop\Code\Reflection\Exception;
use ReflectionException;

/**
 * Reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
class Reflection
{

    /**
     * Create class
     *
     * @param  mixed   $class
     * @param  ?string $name
     * @throws Exception
     * @return Generator\ClassGenerator
     */
    public static function createClass(mixed $class, ?string $name = null): Generator\ClassGenerator
    {
        return Reflection\ClassReflection::parse($class, $name);
    }

    /**
     * Create trait
     *
     * @param  mixed   $trait
     * @param  ?string $name
     * @throws Exception
     * @return Generator\TraitGenerator
     */
    public static function createTrait(mixed $trait, ?string $name = null): Generator\TraitGenerator
    {
        return Reflection\TraitReflection::parse($trait, $name);
    }

    /**
     * Create interface
     *
     * @param  mixed   $interface
     * @param  ?string $name
     * @throws Exception
     * @return Generator\InterfaceGenerator
     */
    public static function createInterface(mixed $interface, ?string $name = null): Generator\InterfaceGenerator
    {
        return Reflection\InterfaceReflection::parse($interface, $name);
    }

    /**
     * Create namespace
     *
     * @param  mixed  $namespace
     * @param  ?string $name
     * @throws Exception
     * @return Generator\NamespaceGenerator
     */
    public static function createNamespace(mixed $namespace, ?string $name = null): Generator\NamespaceGenerator
    {
        return Reflection\NamespaceReflection::parse($namespace, $name);
    }

    /**
     * Create docblock
     *
     * @param  mixed $docblock
     * @param  ?int  $forceIndent
     * @throws Exception
     * @return Generator\DocblockGenerator
     */
    public static function createDocblock(mixed $docblock, ?int $forceIndent = null): Generator\DocblockGenerator
    {
        return Reflection\DocblockReflection::parse($docblock, $forceIndent);
    }

    /**
     * Create function
     *
     * @param  mixed   $function
     * @param  ?string $name
     * @throws ReflectionException
     * @return Generator\FunctionGenerator
     */
    public static function createFunction(mixed $function, ?string $name = null): Generator\FunctionGenerator
    {
        return Reflection\FunctionReflection::parse($function, $name);
    }

    /**
     * Create method
     *
     * @param  mixed   $method
     * @param  ?string $name
     * @return Generator\MethodGenerator
     */
    public static function createMethod(mixed $method, ?string $name = null): Generator\MethodGenerator
    {
        return Reflection\MethodReflection::parse($method, $name);
    }

    /**
     * Create property
     *
     * @param  mixed   $property
     * @param  ?string $name
     * @param  mixed   $value
     * @return Generator\PropertyGenerator
     */
    public static function createProperty(mixed $property, ?string $name = null, mixed $value = null): Generator\PropertyGenerator
    {
        return Reflection\PropertyReflection::parse($property, $name, $value);
    }

}
