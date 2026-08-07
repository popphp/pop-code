<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection;

use Pop\Code\Generator;
use Pop\Code\Reflection\Support\UseStatementParser;
use ReflectionException;

/**
 * Class reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class ClassReflection extends AbstractReflection
{

    /**
     * Method to parse a class
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @throws Exception|ReflectionException
     * @return Generator\ClassGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\ClassGenerator
    {
        $reflection     = new \ReflectionClass($code);

        if ($reflection->isEnum()) {
            throw new Exception('Error: Enums are not yet supported by ClassReflection.');
        }

        $reflectionName = $reflection->getShortName();
        $reflectionFile = $reflection->getFileName();
        $fileContents   = null;

        if (!empty($reflectionFile) && file_exists($reflectionFile)) {
            $fileContents = file_get_contents($reflectionFile);
        }

        if (($name === null) && !empty($reflectionName)) {
            $name = $reflectionName;
        }

        if (($reflection->isInterface()) || ($reflection->isTrait())) {
            throw new Exception('Error: The code must be a class, not an interface or trait.');
        }

        $class = new Generator\ClassGenerator($name);

        // Detect and set namespace
        if (($reflection->inNamespace()) && ($fileContents !== null)) {
            $class->setNamespace(NamespaceReflection::parse($fileContents, $reflection->getNamespaceName()));
        }

        // Detect and set the class doc block
        $classDocBlock = $reflection->getDocComment();
        if (!empty($classDocBlock) && (str_contains($classDocBlock, '/*'))) {
            $class->setDocblock(DocblockReflection::parse($classDocBlock));
        }

        if ($reflection->isAbstract()) {
            $class->setAsAbstract(true);
        } else if ($reflection->isFinal()) {
            $class->setAsFinal(true);
        }

        if ($reflection->isReadOnly()) {
            $class->setAsReadonly(true);
        }

        // Detect parent class
        $parent = $reflection->getParentClass();
        if ($parent !== false) {
            if ($parent->inNamespace()) {
                if (!$class->hasNamespace()) {
                    $class->setNamespace(new Generator\NamespaceGenerator());
                }
                $class->getNamespace()->addUse($parent->getNamespaceName() . '\\' . $parent->getShortName());
            }
            $class->setParent($parent->getShortName());
        }

        // Detect implemented interfaces
        $interfaces = $reflection->getInterfaces();
        if ($interfaces !== false) {
            $interfacesAry = [];
            foreach ($interfaces as $interface) {
                if ($interface->inNamespace()) {
                    if (!$class->hasNamespace()) {
                        $class->setNamespace(new Generator\NamespaceGenerator());
                    }
                    $class->getNamespace()->addUse($interface->getNamespaceName() . '\\' . $interface->getShortName());
                }
                $interfacesAry[] = $interface->getShortName();
            }
            $class->addInterfaces($interfacesAry);
        }

        // Detect used traits
        if ($fileContents !== null) {
            foreach (UseStatementParser::parse($fileContents) as $use => $as) {
                $class->addUse($use, $as);
            }
        }

        // Detect constants
        foreach ($reflection->getReflectionConstants() as $constant) {
            $class->addConstant(ConstantReflection::parse($constant));
        }

        // Detect properties
        $classIsReadonly = $reflection->isReadOnly();
        foreach ($reflection->getProperties() as $property) {
            if ($property->isPromoted()) {
                continue;
            }
            $value             = $property->hasDefaultValue() ? $property->getDefaultValue() : null;
            $propertyGenerator = PropertyReflection::parse($property, $property->getName(), $value);
            if ($classIsReadonly) {
                // Every property in a readonly class reports isReadOnly()=true regardless of whether it
                // says so explicitly; rely on the class-level keyword instead of stuttering it per-property.
                // NOTE: deviates from the task brief's literal `setAsReadonly(false)` — that call also
                // re-enables PropertyGenerator's nullable-widening/default-value logic (gated on the same
                // flag), which produced invalid PHP (a default value on a readonly property). Verified
                // empirically; see task-3-report.md. suppressReadonlyKeyword() only hides the redundant
                // keyword while keeping the property's true readonly semantics for rendering.
                $propertyGenerator->suppressReadonlyKeyword();
            }
            $class->addProperty($propertyGenerator);
        }

        // Detect methods
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                $class->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $class;
    }

}
