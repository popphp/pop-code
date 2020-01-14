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

use Pop\Code\Generator;

/**
 * Property reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class PropertyReflection extends AbstractReflection
{

    /**
     * Method to import code
     *
     * @param  mixed  $code
     * @param  string $name
     * @param  mixed  $value
     * @return Generator\PropertyGenerator
     */
    public static function import($code, $name = null, $value = null)
    {
        if ($code->isProtected()) {
            $visibility = 'protected';
        } else if ($code->isPrivate()) {
            $visibility = 'private';
        } else {
            $visibility = 'public';
        }

        $desc = null;
        $type = null;

        $doc = $code->getDocComment();
        if ((null !== $doc) && (strpos($doc, '/*') !== false)) {
            $docblock = DocblockReflection::import($doc);
            $desc     = $docblock->getDesc();
            $type     = $docblock->getTag('var');
        } else if (null !== $value) {
            $type     = strtolower(gettype($value));
        }

        if (is_array($value)) {
            $formattedValue = (count($value) == 0) ? null : $value;
        } else {
            $formattedValue = $value;
        }

        $property = new Generator\PropertyGenerator($code->getName(), $type, $formattedValue, $visibility, $code->isStatic());
        $property->setDesc($desc);

        return $property;
    }

}