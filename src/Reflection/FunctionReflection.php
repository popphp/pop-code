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

use Pop\Code\Generator\FunctionGenerator;
use Pop\Code\Generator\Literal;
use Pop\Code\Generator\NoValue;
use Pop\Code\Reflection\Support\SourceBodyExtractor;
use ReflectionException;

/**
 * Function reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
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
        $isClosure        = ($reflectionName == '{closure}');

        if (($name === null) && !($isClosure)) {
            $name = $reflectionName;
        }

        $function = new FunctionGenerator($name, $isClosure);

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = $reflectionParam->getType();
            $paramType  = (!empty($paramType) && ($paramType instanceof \ReflectionType)) ? $paramType->getName() : null;

            if (!$reflectionParam->isDefaultValueAvailable()) {
                $paramValue = new NoValue();
            } else if (($constantName = $reflectionParam->getDefaultValueConstantName()) !== null) {
                $paramValue = new Literal($constantName);
            } else {
                $paramValue = $reflectionParam->getDefaultValue();
            }

            $function->addArgument(
                $paramName, $paramValue, $paramType, $reflectionParam->isVariadic(), $reflectionParam->isPassedByReference()
            );
        }

        // Parse the body if available
        $body = SourceBodyExtractor::extract($reflection, false);
        if ($body !== null) {
            $function->setBody($body, 0);
        }

        // Get return type(s)
        if ($reflection->hasReturnType()) {
            $namedTypes  = [];
            $returnTypes = $reflection->getReturnType();
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
                $function->addReturnTypes($namedTypes);
            }
        }

        return $function;
    }

}
