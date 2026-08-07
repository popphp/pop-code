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

use Pop\Code\Generator\DocblockGenerator;

/**
 * Docblock reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class DocblockReflection extends AbstractReflection
{

    /**
     * Method to parse a docblock
     *
     * @param  mixed $code
     * @param  ?int  $forceIndent
     * @throws Exception
     * @return DocblockGenerator
     */
    public static function parse(mixed $code, ?int $forceIndent = null): DocblockGenerator
    {
        if ((!str_contains($code, '/*')) || (!str_contains($code, '*/'))) {
            throw new Exception('The docblock is not in the correct format.');
        }

        $desc          = null;
        $formattedDesc = null;
        $indent        = null;
        $tags          = null;

        // Parse the description, if any. A docblock with no @-tags at all (just a summary line,
        // as is common on enum cases) still has a description to extract, so this must not be
        // gated on the presence of '@' — only the *extent* of the description text depends on it.
        $desc    = str_contains($code, '@') ? substr($code, 0, strpos($code, '@')) : $code;
        $desc    = str_replace('/*', '', $desc);
        $desc    = str_replace('*/', '', $desc);
        $desc    = str_replace(PHP_EOL . ' * ', ' ', $desc);
        $desc    = trim(str_replace('*', '', $desc));
        $descAry = explode("\n", $desc);

        $formattedDesc = null;
        foreach ($descAry as $line) {
            $formattedDesc .= ' ' . trim($line);
        }

        $formattedDesc = trim($formattedDesc);
        if ($formattedDesc === '') {
            $formattedDesc = null;
        }

        // Get the indentation, if any, and create docblock object
        $indent      = (empty($forceIndent)) ? strlen(substr($code, 0, strpos($code, '/'))) : $forceIndent;
        $docblock = new DocblockGenerator($formattedDesc, $indent);

        // Get the tags, if any
        if (str_contains($code, '@')) {
            $tags    = substr($code, strpos($code, '@'));
            $tags    = substr($tags, 0, strpos($tags, '*/'));
            $tags    = str_replace('*', '', $tags);
            $tagsAry = explode("\n", $tags);

            foreach ($tagsAry as $key => $value) {
                $value = trim(str_replace('@', '', $value));
                // Param tags
                if (stripos($value, 'param') !== false) {
                    $paramTag  = trim(str_replace('param', '', $value));
                    $paramType = trim(substr($paramTag, 0, strpos($paramTag, ' ')));
                    $varName   = null;
                    $paramDesc = null;
                    if (str_contains($paramTag, ' ')) {
                        $varName = trim(substr($paramTag, strpos($paramTag, ' ')));
                        if (str_contains($varName, ' ')) {
                            $paramDesc = trim(substr($varName, strpos($varName, ' ')));
                        }
                    } else if (str_starts_with($paramTag, '$')) {
                        // A bare "@param $var" with no type at all -- $paramTag is the variable
                        // name, not a type. Without this check it fell into the branch below and
                        // was stored as the type instead, leaving 'var' unset -- which meant a
                        // later addArgument() call for the same parameter (which correctly
                        // computes the real variable name) couldn't recognize this as the same
                        // param to replace, and appended a second @param line instead.
                        $paramType = null;
                        $varName   = $paramTag;
                    } else {
                        $paramType = $paramTag;
                    }
                    $docblock->addParam($paramType, $varName, $paramDesc);
                // Else, return tags
                } else if (stripos($value, 'return') !== false) {
                    $returnTag = trim(str_replace('return', '', $value));
                    if (str_contains($returnTag, ' ')) {
                        $returnType = substr($returnTag, 0, strpos($returnTag, ' '));
                        $returnDesc = trim(str_replace($returnType, '', $returnTag));
                    } else {
                        $returnType = $returnTag;
                        $returnDesc = null;
                    }
                    $docblock->setReturn($returnType, $returnDesc);
                // Else, all other tags
                } else {
                    $tagName = trim(substr($value, 0, strpos($value, ' ')));
                    $tagDesc = trim(str_replace($tagName, '', $value));
                    if (!empty($tagName) && !empty($tagDesc)) {
                        $docblock->addTag($tagName, $tagDesc);
                    } else {
                        unset($tagsAry[$key]);
                    }
                }
            }
        }

        return $docblock;
    }

}
