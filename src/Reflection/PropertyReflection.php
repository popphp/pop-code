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
use Pop\Code\Reflection\Support\TypeNormalizer;

/**
 * Property reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
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
        if (!$property->hasType()) {
            return null;
        }

        $reflectionType = $property->getType();
        $namedTypes     = [];

        if ($reflectionType instanceof \ReflectionUnionType) {
            foreach ($reflectionType->getTypes() as $namedType) {
                $namedTypes[] = $namedType->getName();
            }
        } else if ($reflectionType instanceof \ReflectionNamedType) {
            $namedTypes[] = $reflectionType->getName();
        }

        if (!in_array('mixed', $namedTypes, true) && $reflectionType->allowsNull() && !in_array('null', $namedTypes, true)) {
            $namedTypes[] = 'null';
        }

        return implode('|', $namedTypes);
    }

}
