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
namespace Pop\Code\Reflection;

use Pop\Code\Generator;
use ReflectionException;

/**
 * Class reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
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
            $uses = [];
            preg_match_all('/[ ]+use(.*);$/m', $fileContents, $uses);

            if (isset($uses[1])) {
                foreach ($uses[1] as $u) {
                    $useAry = array_map('trim', explode(',', trim($u)));
                    foreach ($useAry as $useValue) {
                        if (strpos($useValue, ' as ') !== false) {
                            [$use, $as] = explode(' as ', $useValue);
                        } else {
                            $use = $useValue;
                            $as  = null;
                        }
                        $class->addUse($use, $as);
                    }
                }
            }
        }

        // Detect constants
        $constants = $reflection->getConstants();
        if (count($constants) > 0) {
            foreach ($constants as $key => $value) {
                $class->addConstant(new Generator\ConstantGenerator($key, gettype($value), $value));
            }
        }

        // Detect properties
        $properties = $reflection->getDefaultProperties();
        if (count($properties) > 0) {
            foreach ($properties as $name => $value) {
                $class->addProperty(PropertyReflection::parse($reflection->getProperty($name), $name, $value));
            }
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
