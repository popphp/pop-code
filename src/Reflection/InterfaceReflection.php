<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection;

use Pop\Code\Generator;
use ReflectionException;

/**
 * Interface reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
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

        // Detect and set the class doc block
        $interfaceDocBlock = $reflection->getDocComment();
        if (!empty($interfaceDocBlock) && (str_contains($interfaceDocBlock, '/*'))) {
            $interface->setDocblock(DocblockReflection::parse($interfaceDocBlock));
        }

        // Detect parent class
        $parent = $reflection->getParentClass();
        if ($parent !== false) {
            if ($parent->inNamespace()) {
                if (!$interface->hasNamespace()) {
                    $interface->setNamespace(new Generator\NamespaceGenerator());
                }
                $interface->getNamespace()->addUse($parent->getNamespaceName() . '\\' . $parent->getShortName());
            }
            $interface->setParent($parent->getShortName());
        }

        // Detect constants
        $constants = $reflection->getConstants();
        if (count($constants) > 0) {
            foreach ($constants as $key => $value) {
                $interface->addConstant(new Generator\ConstantGenerator($key, gettype($value), $value));
            }
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