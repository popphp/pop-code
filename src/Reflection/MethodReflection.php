<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2021 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection;

use Pop\Code\Generator;

/**
 * Method reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2021 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
class MethodReflection extends AbstractReflection
{

    /**
     * Method to parse a method
     *
     * @param  mixed  $code
     * @param  string $name
     * @return Generator\MethodGenerator
     */
    public static function parse($code, $name = null)
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
        if ((null !== $doc) && (strpos($doc, '/*') !== false)) {
            $docblock = DocblockReflection::parse($doc);
            $docblock->setIndent(4);
        }

        $method = new Generator\MethodGenerator($code->getName(), $visibility, $code->isStatic());
        if (null !== $docblock) {
            $method->setDocblock($docblock);
        }

        if ($code->isAbstract()) {
            $method->setAsAbstract(true);
        } else if ($code->isFinal()) {
            $method->setAsFinal(true);
        }

        $reflectionParams = $code->getParameters();

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = $reflectionParam->getType();
            $paramType  = (!empty($paramType) && ($paramType instanceof \ReflectionType)) ? $paramType->getName() : null;

            try {
                $paramValue = $reflectionParam->getDefaultValue();
            } catch (\ReflectionException $e) {
                $paramValue = null;
            }

            $method->addArgument($paramName, $paramValue, $paramType);
        }

        // Parse the body if available
        $file = $code->getFileName();

        if (!empty($file) && file_exists($file)) {
            $lines     = file($file);
            $startLine = $code->getStartLine() - 1;
            $endLine   = $code->getEndLine() - 1;
            $length    = $endLine - $startLine;
            $body      = null;

            if (($length > 0) && isset($lines[$startLine]) && isset($lines[$endLine])) {
                $lines = array_slice($lines, ($startLine + 1), $length);

                if (preg_match('/[ ]+\}/', $lines[(count($lines) - 1)])) {
                    unset($lines[(count($lines) - 1)]);
                }
                if (isset($lines[0]) && preg_match('/[ ]+\{/', $lines[0])) {
                    unset($lines[0]);
                }

                $lines = array_values($lines);

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
                $method->setBody($body);
            }
        }

        return $method;
    }

}