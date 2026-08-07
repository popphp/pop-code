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
use Pop\Code\Reflection\Support\AttributeCollector;
use Pop\Code\Reflection\Support\NamespaceImportResolver;

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

        // Shared across enum-level attributes, interfaces, and case-level attributes below --
        // see NamespaceImportResolver.
        $importResolver = new NamespaceImportResolver();

        // Detect attributes
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            [$attributeReference, $needsImport] = $importResolver->resolve($reflectionAttribute->getName(), $reflection->getNamespaceName());
            if ($needsImport) {
                if (!$enum->hasNamespace()) {
                    $enum->setNamespace(new Generator\NamespaceGenerator());
                }
                $enum->getNamespace()->addUse($reflectionAttribute->getName());
            }
            $enum->addAttribute(AttributeCollector::build($reflectionAttribute, $attributeReference));
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
                [$interfaceReference, $needsImport] = $importResolver->resolve($interface->getName(), $reflection->getNamespaceName());
                if ($needsImport) {
                    if (!$enum->hasNamespace()) {
                        $enum->setNamespace(new Generator\NamespaceGenerator());
                    }
                    $enum->getNamespace()->addUse($interface->getName());
                }
                $interfacesAry[] = $interfaceReference;
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
                $caseDocblock = DocblockReflection::parse($caseDocBlock);
                $caseDocblock->setIndent(4);
                $enumCase->setDocblock($caseDocblock);
            }

            foreach ($case->getAttributes() as $reflectionAttribute) {
                [$attributeReference, $needsImport] = $importResolver->resolve($reflectionAttribute->getName(), $reflection->getNamespaceName());
                if ($needsImport) {
                    if (!$enum->hasNamespace()) {
                        $enum->setNamespace(new Generator\NamespaceGenerator());
                    }
                    $enum->getNamespace()->addUse($reflectionAttribute->getName());
                }
                $enumCase->addAttribute(AttributeCollector::build($reflectionAttribute, $attributeReference));
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

        // Detect methods, skipping the internal cases()/from()/tryFrom() methods PHP synthesizes
        // on every enum (backed enums get from()/tryFrom() too) -- re-emitting these as empty
        // user-declared methods causes a fatal "Cannot redeclare" error when the generated code
        // is loaded.
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                if ($method->isInternal()) {
                    continue;
                }
                $enum->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $enum;
    }

}
