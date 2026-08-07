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
use Pop\Code\Reflection\Support\AttributeCollector;
use ReflectionException;

/**
 * Interface reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class InterfaceReflection extends AbstractReflection
{

    /**
     * Method to parse an interface
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @throws Exception|ReflectionException
     * @return Generator\InterfaceGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\InterfaceGenerator
    {
        $reflection     = new \ReflectionClass($code);
        $reflectionName = $reflection->getShortName();

        if (($name === null) && !empty($reflectionName)) {
            $name = $reflectionName;
        }

        if (!$reflection->isInterface()) {
            throw new Exception('Error: The code is not an interface.');
        }

        $interface = new Generator\InterfaceGenerator($name);

        // Detect and set namespace
        if ($reflection->inNamespace()) {
            $file = $reflection->getFileName();
            if (!empty($file) && file_exists($file)) {
                $interface->setNamespace(NamespaceReflection::parse(file_get_contents($file), $reflection->getNamespaceName()));
            }
        }

        // Detect attributes
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            $attributeName = $reflectionAttribute->getName();
            if (str_contains($attributeName, '\\')
                && (substr($attributeName, 0, strrpos($attributeName, '\\')) !== $reflection->getNamespaceName())
            ) {
                if (!$interface->hasNamespace()) {
                    $interface->setNamespace(new Generator\NamespaceGenerator());
                }
                $interface->getNamespace()->addUse($attributeName);
            }
            $interface->addAttribute(AttributeCollector::build($reflectionAttribute));
        }

        // Detect and set the class doc block
        $interfaceDocBlock = $reflection->getDocComment();
        if (!empty($interfaceDocBlock) && (str_contains($interfaceDocBlock, '/*'))) {
            $interface->setDocblock(DocblockReflection::parse($interfaceDocBlock));
        }

        // Detect parent interface(s) -- for an interface, getParentClass() always returns false
        // (that API is for class `extends`); the interfaces it extends are reported via
        // getInterfaces() instead. InterfaceGenerator only supports a single parent today, so
        // only the first is used if the interface extends more than one -- a pre-existing
        // generator-side limitation, not something introduced by this fix.
        $parents = $reflection->getInterfaces();
        if (!empty($parents)) {
            $parent = reset($parents);
            if ($parent->inNamespace() && ($parent->getNamespaceName() !== $reflection->getNamespaceName())) {
                if (!$interface->hasNamespace()) {
                    $interface->setNamespace(new Generator\NamespaceGenerator());
                }
                $interface->getNamespace()->addUse($parent->getNamespaceName() . '\\' . $parent->getShortName());
            }
            $interface->setParent($parent->getShortName());
        }

        // Detect constants
        foreach ($reflection->getReflectionConstants() as $constant) {
            $interface->addConstant(ConstantReflection::parse($constant));
        }

        // Detect methods
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                $interface->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $interface;
    }

}
