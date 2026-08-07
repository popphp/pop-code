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

/**
 * Enum reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class EnumReflection extends AbstractReflection
{

    /**
     * Method to parse an enum
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @return Generator\EnumGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\EnumGenerator
    {
        $reflection     = new \ReflectionEnum($code);
        $reflectionName = $reflection->getShortName();
        $reflectionFile = $reflection->getFileName();
        $fileContents   = null;

        if (!empty($reflectionFile) && file_exists($reflectionFile)) {
            $fileContents = file_get_contents($reflectionFile);
        }

        if (($name === null) && !empty($reflectionName)) {
            $name = $reflectionName;
        }

        $enum = new Generator\EnumGenerator($name);

        if ($reflection->isBacked()) {
            $enum->setBackingType((string) $reflection->getBackingType());
        }

        // Detect and set namespace
        if (($reflection->inNamespace()) && ($fileContents !== null)) {
            $enum->setNamespace(NamespaceReflection::parse($fileContents, $reflection->getNamespaceName()));
        }

        // Detect and set the enum doc block
        $enumDocBlock = $reflection->getDocComment();
        if (!empty($enumDocBlock) && (str_contains($enumDocBlock, '/*'))) {
            $enum->setDocblock(DocblockReflection::parse($enumDocBlock));
        }

        // Detect implemented interfaces, excluding the implicitly-added UnitEnum/BackedEnum
        $interfaces = $reflection->getInterfaces();
        if ($interfaces !== false) {
            $interfacesAry = [];
            foreach ($interfaces as $interface) {
                if (in_array($interface->getName(), ['UnitEnum', 'BackedEnum'], true)) {
                    continue;
                }
                if ($interface->inNamespace()) {
                    if (!$enum->hasNamespace()) {
                        $enum->setNamespace(new Generator\NamespaceGenerator());
                    }
                    $enum->getNamespace()->addUse($interface->getNamespaceName() . '\\' . $interface->getShortName());
                }
                $interfacesAry[] = $interface->getShortName();
            }
            $enum->addInterfaces($interfacesAry);
        }

        // Detect used traits
        if ($fileContents !== null) {
            foreach (UseStatementParser::parse($fileContents) as $use => $as) {
                $enum->addUse($use, $as);
            }
        }

        // Detect cases
        $caseNames = [];
        foreach ($reflection->getCases() as $case) {
            $caseNames[] = $case->getName();

            $value    = ($case instanceof \ReflectionEnumBackedCase) ? $case->getBackingValue() : null;
            $enumCase = new Generator\EnumCaseGenerator($case->getName(), $value);

            $caseDocBlock = $case->getDocComment();
            if (!empty($caseDocBlock) && (str_contains($caseDocBlock, '/*'))) {
                $enumCase->setDocblock(DocblockReflection::parse($caseDocBlock));
            }

            $enum->addCase($enumCase);
        }

        // Detect constants, excluding cases (getReflectionConstants() reports both)
        foreach ($reflection->getReflectionConstants() as $constant) {
            if (in_array($constant->getName(), $caseNames, true)) {
                continue;
            }
            $enum->addConstant(ConstantReflection::parse($constant));
        }

        // Detect methods
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                $enum->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $enum;
    }

}
