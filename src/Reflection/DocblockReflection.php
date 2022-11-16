<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
class DocblockReflection extends AbstractReflection
{

    /**
     * Method to parse a docblock
     *
     * @param  string $code
     * @param  int    $forceIndent
     * @throws Exception
     * @return DocblockGenerator
     */
    public static function parse($code, $forceIndent = null)
    {
        if ((strpos($code, '/*') === false) || (strpos($code, '*/') === false)) {
            throw new Exception('The docblock is not in the correct format.');
        }

        $desc          = null;
        $formattedDesc = null;
        $indent        = null;
        $tags          = null;

        // Parse the description, if any
        if (strpos($code, '@') !== false) {
            $desc    = substr($code, 0, strpos($code, '@'));
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
        }

        // Get the indentation, if any, and create docblock object
        $indent      = (empty($forceIndent)) ? strlen(substr($code, 0, strpos($code, '/'))) : $forceIndent;
        $docblock = new DocblockGenerator($formattedDesc, $indent);

        // Get the tags, if any
        if (strpos($code, '@') !== false) {
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
                    if (strpos($paramTag, ' ') !== false) {
                        $varName = trim(substr($paramTag, strpos($paramTag, ' ')));
                        if (strpos($varName, ' ') !== false) {
                            $paramDesc = trim(substr($varName, strpos($varName, ' ')));
                        }
                    } else {
                        $paramType = $paramTag;
                    }
                    $docblock->addParam($paramType, $varName, $paramDesc);
                // Else, return tags
                } else if (stripos($value, 'return') !== false) {
                    $returnTag = trim(str_replace('return', '', $value));
                    if (strpos($returnTag, ' ') !== false) {
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