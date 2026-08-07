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
            throw new Exception('Error: The code is an enum; use Reflection::createEnum() instead.');
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

        // Shared across attributes, the parent class, and interfaces below, so a same-short-name
        // collision between e.g. the parent class and an attribute is caught too, not just among
        // attributes alone. See NamespaceImportResolver for why: two `use` statements for
        // different classes sharing one short name is a PHP fatal error, so the second one must
        // fall back to a fully-qualified reference instead of colliding.
        $importResolver = new NamespaceImportResolver();

        // Detect attributes
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            [$attributeReference, $needsImport] = $importResolver->resolve($reflectionAttribute->getName(), $reflection->getNamespaceName());
            if ($needsImport) {
                if (!$class->hasNamespace()) {
                    $class->setNamespace(new Generator\NamespaceGenerator());
                }
                $class->getNamespace()->addUse($reflectionAttribute->getName());
            }
            $class->addAttribute(AttributeCollector::build($reflectionAttribute, $attributeReference));
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
            [$parentReference, $needsImport] = $importResolver->resolve($parent->getName(), $reflection->getNamespaceName());
            if ($needsImport) {
                if (!$class->hasNamespace()) {
                    $class->setNamespace(new Generator\NamespaceGenerator());
                }
                $class->getNamespace()->addUse($parent->getName());
            }
            $class->setParent($parentReference);
        }

        // Detect implemented interfaces -- getInterfaces() returns the full transitive closure
        // (every interface reachable via this class, its parent chain, and any interface's own
        // extends), not just what this class itself directly declares in `implements`. A
        // candidate is kept only if it isn't already provided by the parent class (inherited, not
        // re-declared) and isn't reachable via another candidate already in this class's own set
        // (implied by that candidate's own extends, not itself a distinct direct implements).
        $interfaces = $reflection->getInterfaces();
        if ($interfaces !== false) {
            $parentInterfaceNames = ($parent !== false) ? $parent->getInterfaceNames() : [];
            $interfacesAry        = [];
            foreach ($interfaces as $candidateName => $interface) {
                if (in_array($candidateName, $parentInterfaceNames, true)) {
                    continue;
                }
                $isTransitive = false;
                foreach ($interfaces as $otherName => $other) {
                    if (($otherName !== $candidateName) && in_array($candidateName, $other->getInterfaceNames(), true)) {
                        $isTransitive = true;
                        break;
                    }
                }
                if ($isTransitive) {
                    continue;
                }

                [$interfaceReference, $needsImport] = $importResolver->resolve($candidateName, $reflection->getNamespaceName());
                if ($needsImport) {
                    if (!$class->hasNamespace()) {
                        $class->setNamespace(new Generator\NamespaceGenerator());
                    }
                    $class->getNamespace()->addUse($candidateName);
                }
                $interfacesAry[] = $interfaceReference;
            }
            $class->addInterfaces($interfacesAry);
        }

        // Detect used traits
        if ($fileContents !== null) {
            foreach (UseStatementParser::parse($fileContents) as $use => $as) {
                $class->addUse($use, $as);
            }
        }

        // Detect constants -- getReflectionConstants() includes inherited constants; keep only
        // ones actually declared on this class (a trait-provided constant still reports its
        // declaring class as this one, since PHP flattens trait members into the using class, so
        // this filter only excludes constants inherited from a parent class, not trait ones).
        foreach ($reflection->getReflectionConstants() as $constant) {
            if ($constant->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }
            $class->addConstant(ConstantReflection::parse($constant));
        }

        // Detect properties -- getProperties() includes inherited properties; same declaring-class
        // filter as constants above, with the same trait-member caveat.
        $classIsReadonly = $reflection->isReadOnly();
        foreach ($reflection->getProperties() as $property) {
            if ($property->isPromoted() || ($property->getDeclaringClass()->getName() !== $reflection->getName())) {
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

        // Detect methods -- getMethods() includes inherited methods; same declaring-class filter,
        // same trait-member caveat. An overridden method (e.g. implementing an abstract parent
        // method) still reports its declaring class as this one, since the override itself is a
        // real declaration here.
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                    continue;
                }
                $class->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $class;
    }

}
