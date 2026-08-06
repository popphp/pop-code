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
namespace Pop\Code\Reflection\Support;

/**
 * Source body extractor class
 *
 * Recovers a method/function/closure's body as source text by slicing its declaring file between
 * getStartLine()/getEndLine(). Both ReflectionMethod and ReflectionFunction extend
 * ReflectionFunctionAbstract, so one helper serves MethodReflection and FunctionReflection, which
 * previously each carried a near-identical, independently written copy of this logic.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class SourceBodyExtractor
{

    /**
     * Extract a method/function's body as source text
     *
     * @param  \ReflectionFunctionAbstract $reflection
     * @param  bool                        $stripBraces
     * @return string|null
     */
    public static function extract(\ReflectionFunctionAbstract $reflection, bool $stripBraces): string|null
    {
        $file = $reflection->getFileName();

        if (empty($file) || !file_exists($file)) {
            return null;
        }

        $lines     = file($file);
        $startLine = $reflection->getStartLine() - 1;
        $endLine   = $reflection->getEndLine() - 1;
        $length    = $endLine - $startLine;

        if (($length <= 0) || !isset($lines[$startLine]) || !isset($lines[$endLine])) {
            return null;
        }

        if ($stripBraces) {
            $lines = array_slice($lines, $startLine + 1, $length);

            if (preg_match('/[ ]+\}/', $lines[count($lines) - 1])) {
                unset($lines[count($lines) - 1]);
            }
            if (isset($lines[0]) && preg_match('/[ ]+\{/', $lines[0])) {
                unset($lines[0]);
            }

            $lines = array_values($lines);
        } else {
            $lines = array_slice($lines, $startLine + 1, $length - 1);
        }

        if (isset($lines[0]) && str_starts_with($lines[0], ' ')) {
            $spaces = strlen($lines[0]) - strlen(ltrim($lines[0]));
            if ($spaces > 0) {
                $lines = array_map(function ($value) use ($spaces) {
                    if (substr($value, 0, $spaces) === str_repeat(' ', $spaces)) {
                        $value = substr($value, $spaces);
                    }
                    return $value;
                }, $lines);
            }
        }

        $body = implode('', $lines);

        return empty($body) ? null : $body;
    }

}
