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
use Pop\Code\Reflection\Support\AttributeCollector;
use Pop\Code\Reflection\Support\SourceBodyExtractor;
use Pop\Code\Reflection\Support\TypeNormalizer;

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
        foreach ($code->getAttributes() as $reflectionAttribute) {
            $method->addAttribute(AttributeCollector::build($reflectionAttribute));
        }
        if ($docblock !== null) {
            $method->setDocblock($docblock);
        }

        $reflectionParams = $code->getParameters();
        $declaringClass   = $code->getDeclaringClass();

        // An interface method is always reported abstract by native reflection, but the
        // `abstract` keyword is implicit -- and syntactically forbidden -- inside an interface
        // body. Only apply the flag for a real abstract method on a class.
        if ($code->isAbstract() && !$declaringClass->isInterface()) {
            $method->setAsAbstract(true);
        } else if ($code->isFinal()) {
            $method->setAsFinal(true);
        }

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = TypeNormalizer::resolveReflectionType($reflectionParam->getType());

            if (!$reflectionParam->isDefaultValueAvailable()) {
                $paramValue = new NoValue();
            } else if (($constantName = $reflectionParam->getDefaultValueConstantName()) !== null) {
                $paramValue = new Literal($constantName);
            } else {
                $paramValue = $reflectionParam->getDefaultValue();
            }

            $paramAttributes = [];
            foreach ($reflectionParam->getAttributes() as $reflectionAttribute) {
                $paramAttributes[] = AttributeCollector::build($reflectionAttribute);
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
                $method->addPromotedArgument(
                    $paramName, $promotedVisibility, $paramValue, $paramType, $promotedProperty->isReadOnly(), $paramAttributes
                );
            } else {
                $method->addArgument(
                    $paramName, $paramValue, $paramType, $reflectionParam->isVariadic(), $reflectionParam->isPassedByReference(),
                    $paramAttributes
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
        $returnType = TypeNormalizer::resolveReflectionType($code->getReturnType());
        if ($returnType !== null) {
            $method->addReturnType($returnType);
        }

        return $method;
    }

}
