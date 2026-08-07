<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection;

use Pop\Code\Generator\FunctionGenerator;
use Pop\Code\Generator\Literal;
use Pop\Code\Generator\NoValue;
use Pop\Code\Reflection\Support\AttributeCollector;
use Pop\Code\Reflection\Support\SourceBodyExtractor;
use Pop\Code\Reflection\Support\TypeNormalizer;
use ReflectionException;

/**
 * Function reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class FunctionReflection extends AbstractReflection
{

    /**
     * Method to parse a function or closure
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @throws ReflectionException
     * @return FunctionGenerator
     */
    public static function parse(mixed $code, ?string $name = null): FunctionGenerator
    {
        $reflection       = new \ReflectionFunction($code);
        $reflectionName   = $reflection->getName();
        $reflectionParams = $reflection->getParameters();
        // PHP 8.4 renamed closures from the bare '{closure}' to '{closure:file:line}' (or
        // '{closure:Class::method():line}' for one declared inside a method) -- match the prefix
        // rather than the exact old name, or isClosure is never true on this library's own
        // minimum supported PHP version, and the mangled name gets used as a literal function
        // name in the rendered output instead of being detected as a closure at all.
        $isClosure        = str_starts_with($reflectionName, '{closure');

        if (($name === null) && !($isClosure)) {
            $name = $reflectionName;
        }

        $function = new FunctionGenerator($name, $isClosure);
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            $function->addAttribute(AttributeCollector::build($reflectionAttribute));
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

            $function->addArgument(
                $paramName, $paramValue, $paramType, $reflectionParam->isVariadic(), $reflectionParam->isPassedByReference(),
                $paramAttributes
            );
        }

        // Parse the body if available
        $body = SourceBodyExtractor::extract($reflection, false);
        if ($body !== null) {
            $function->setBody($body, 0);
        }

        // Get return type(s)
        $returnType = TypeNormalizer::resolveReflectionType($reflection->getReturnType());
        if ($returnType !== null) {
            $function->addReturnType($returnType);
        }

        return $function;
    }

}
