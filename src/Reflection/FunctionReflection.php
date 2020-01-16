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

use Pop\Code\Generator\FunctionGenerator;

/**
 * Function reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class FunctionReflection extends AbstractReflection
{

    /**
     * Method to parse a function or closure
     *
     * @param  mixed  $code
     * @param  string $name
     * @throws \ReflectionException
     * @return FunctionGenerator
     */
    public static function parse($code, $name = null)
    {
        $reflection       = new \ReflectionFunction($code);
        $reflectionName   = $reflection->getName();
        $reflectionParams = $reflection->getParameters();
        $isClosure        = ($reflectionName == '{closure}');

        if ((null === $name) && !($isClosure)) {
            $name = $reflectionName;
        }

        $function = new FunctionGenerator($name, $isClosure);

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = $reflectionParam->getType();
            $paramType  = (!empty($paramType) && ($paramType instanceof \ReflectionType)) ? $paramType->getName() : null;

            try {
                $paramValue = $reflectionParam->getDefaultValue();
            } catch (\ReflectionException $e) {
                $paramValue = null;
            }

            $function->addArgument($paramName, $paramValue, $paramType);
        }

        // Parse the body if available
        $file = $reflection->getFileName();

        if (!empty($file) && file_exists($file)) {
            $lines     = file($file);
            $startLine = $reflection->getStartLine() - 1;
            $endLine   = $reflection->getEndLine() - 1;
            $length    = $endLine - $startLine;
            $body      = null;

            if (($length > 0) && isset($lines[$startLine]) && isset($lines[$endLine])) {
                $lines = array_slice($lines, ($startLine + 1), ($length - 1));
                if (isset($lines[0]) && (substr($lines[0], 0, 1) == ' ')) {
                    $spaces = strlen($lines[0]) - strlen(ltrim($lines[0]));
                    if ($spaces > 0) {
                        $lines = array_map(function($value) use ($spaces) {
                            if (substr($value, 0, $spaces) == str_repeat(' ', $spaces)) {
                                $value = substr($value, $spaces);
                            }
                            return $value;
                        }, $lines);
                    }
                }
                $body = implode('', $lines);
            }

            if (!empty($body)) {
                $function->setBody($body, false);
            }
        }

        return $function;
    }

}