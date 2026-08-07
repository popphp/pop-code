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
use Pop\Code\Generator\Literal;
use Pop\Code\Generator\NoValue;
use Pop\Code\Reflection\Support\SourceBodyExtractor;

/**
 * Method reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class MethodReflection extends AbstractReflection
{

    /**
     * Method to parse a method
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @return Generator\MethodGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\MethodGenerator
    {
        if ($code->isProtected()) {
            $visibility = 'protected';
        } else if ($code->isPrivate()) {
            $visibility = 'private';
        } else {
            $visibility = 'public';
        }

        $docblock = null;
        $doc      = $code->getDocComment();
        if (($doc !== null) && (str_contains($doc, '/*'))) {
            $docblock = DocblockReflection::parse($doc);
            $docblock->setIndent(4);
        }

        $method = new Generator\MethodGenerator($code->getName(), $visibility, $code->isStatic());
        if ($docblock !== null) {
            $method->setDocblock($docblock);
        }

        if ($code->isAbstract()) {
            $method->setAsAbstract(true);
        } else if ($code->isFinal()) {
            $method->setAsFinal(true);
        }

        $reflectionParams = $code->getParameters();
        $declaringClass   = $code->getDeclaringClass();

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = $reflectionParam->getType();
            $paramType  = (!empty($paramType) && ($paramType instanceof \ReflectionType) &&
                method_exists($paramType, 'getName')) ? $paramType->getName() : null;

            if (!$reflectionParam->isDefaultValueAvailable()) {
                $paramValue = new NoValue();
            } else if (($constantName = $reflectionParam->getDefaultValueConstantName()) !== null) {
                $paramValue = new Literal($constantName);
            } else {
                $paramValue = $reflectionParam->getDefaultValue();
            }

            if ($reflectionParam->isPromoted()) {
                $promotedProperty = $declaringClass->getProperty($paramName);
                if ($promotedProperty->isProtected()) {
                    $promotedVisibility = 'protected';
                } else if ($promotedProperty->isPrivate()) {
                    $promotedVisibility = 'private';
                } else {
                    $promotedVisibility = 'public';
                }
                $method->addPromotedArgument($paramName, $promotedVisibility, $paramValue, $paramType, $promotedProperty->isReadOnly());
            } else {
                $method->addArgument(
                    $paramName, $paramValue, $paramType, $reflectionParam->isVariadic(), $reflectionParam->isPassedByReference()
                );
            }
        }

        // Parse the body if available. Concrete methods always get an explicit body (even if
        // empty) so they render with braces rather than as a bodyless abstract/interface stub.
        $body = SourceBodyExtractor::extract($code, true);
        if (!$code->isAbstract()) {
            $method->setBody($body ?? '');
        }

        // Get return type(s)
        if ($code->hasReturnType()) {
            $namedTypes  = [];
            $returnTypes = $code->getReturnType();
            if ($returnTypes instanceof \ReflectionUnionType) {
                $types = $returnTypes->getTypes();
                foreach ($types as $type) {
                    $namedTypes[] = $type->getName();
                }
                if (($returnTypes->allowsNull()) && !in_array('null', $namedTypes)) {
                    $namedTypes[] = 'null';
                }
            } else if ($returnTypes instanceof \ReflectionNamedType) {
                $namedTypes[] = $returnTypes->getName();
                if (($returnTypes->allowsNull()) && !in_array('null', $namedTypes)) {
                    $namedTypes[] = 'null';
                }
            }
            if (!empty($namedTypes)) {
                $method->addReturnTypes($namedTypes);
            }
        }

        return $method;
    }

}
