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

use Pop\Code\Generator;
use Pop\Code\Reflection\Support\TypeNormalizer;
use Pop\Code\Reflection\Support\AttributeCollector;

/**
 * Property reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class PropertyReflection extends AbstractReflection
{

    /**
     * Method to parse a property
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @param  mixed   $value
     * @return Generator\PropertyGenerator
     */
    public static function parse(mixed $code, ?string $name = null, mixed $value = null): Generator\PropertyGenerator
    {
        if ($code->isProtected()) {
            $visibility = 'protected';
        } else if ($code->isPrivate()) {
            $visibility = 'private';
        } else {
            $visibility = 'public';
        }

        $docblock = null;
        $desc     = null;
        $type     = self::resolveType($code);

        $doc = $code->getDocComment();
        if (($doc !== null) && (str_contains($doc, '/*'))) {
            $docblock = DocblockReflection::parse($doc);
            $docblock->setIndent(4);
            $desc = $docblock->getDesc();
            if ($type === null) {
                $type = $docblock->getTag('var');
            }
        }

        if (($type === null) && ($value !== null)) {
            $type = TypeNormalizer::normalize(strtolower(gettype($value)));
        }

        if (is_array($value)) {
            $formattedValue = (count($value) == 0) ? null : $value;
        } else {
            $formattedValue = $value;
        }

        $property = new Generator\PropertyGenerator($code->getName(), $type, $formattedValue, $visibility, $code->isStatic());
        $property->setAsReadonly($code->isReadOnly());
        if ($docblock !== null) {
            $property->setDocblock($docblock);
        }
        $property->setDesc($desc);

        foreach ($code->getAttributes() as $reflectionAttribute) {
            $property->addAttribute(AttributeCollector::build($reflectionAttribute));
        }

        return $property;
    }

    /**
     * Resolve a property's declared type (if any) into a bare, pipe-joined type-hint string
     *
     * @param  \ReflectionProperty $property
     * @return string|null
     */
    protected static function resolveType(\ReflectionProperty $property): string|null
    {
        return $property->hasType() ? TypeNormalizer::resolveReflectionType($property->getType()) : null;
    }

}
