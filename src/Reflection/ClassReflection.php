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
namespace Pop\Code\Reflection;

use Pop\Code\Generator;
use Pop\Code\Reflection;
use Pop\Code\Generator\InterfaceGenerator as IGen;

/**
 * Class reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class ClassReflection extends AbstractReflection
{

    /**
     * Method to parse a function or closure
     *
     * @param  mixed  $code
     * @param  string $name
     * @return Generator\ClassGenerator
     */
    public static function import($code, $name = null)
    {
        $reflection     = new \ReflectionClass($code);
        $reflectionName = $reflection->getShortName();

        if ((null === $name) && !empty($reflectionName)) {
            $name = $reflectionName;
        }

        $class = new Generator\ClassGenerator($name);

        // Detect and set namespace
        if ($reflection->inNamespace()) {
            $file = $reflection->getFileName();
            if (!empty($file) && file_exists($file)) {
                $class->setNamespace(NamespaceReflection::import(file_get_contents($file), $reflection->getNamespaceName()));
            }
        }

        // Detect and set the class doc block
        $classDocBlock = $reflection->getDocComment();
        if (!empty($classDocBlock) && (strpos($classDocBlock, '/*') !== false)) {
            $class->setDocblock(DocblockReflection::import($classDocBlock));
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
                $class->addProperty(PropertyReflection::import($reflection->getProperty($name), $name, $value));
            }
        }

        // Detect methods
        $methods = $reflection->getMethods();

        if (count($methods) > 0) {
            foreach ($methods as $value) {
                $methodExport = \ReflectionMethod::export($value->class, $value->name, true);
                $class->addMethod(MethodReflection::import($methodExport, $value->name));
            }
        }

        return $class;
    }

}