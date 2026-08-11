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
namespace Pop\Code\Reflection\Support;

/**
 * Type normalizer class
 *
 * Maps gettype()'s legacy type names to the PHP type-hint keyword they should have been. gettype()
 * returns 'integer'/'boolean'/'double', none of which are valid in a PHP type-hint position — using
 * them there directly is what produced invalid generated code before this fix.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class TypeNormalizer
{

    /**
     * Map of gettype() names to PHP type-hint keywords
     * @var array
     */
    protected const MAP = [
        'integer' => 'int',
        'boolean' => 'bool',
        'double'  => 'float',
        'NULL'    => 'null',
    ];

    /**
     * Normalize a gettype()-style name to a PHP type-hint keyword
     *
     * @param  string $gettypeName
     * @return string
     */
    public static function normalize(string $gettypeName): string
    {
        return self::MAP[$gettypeName] ?? $gettypeName;
    }

    /**
     * Resolve a native ReflectionType into a bare, pipe-joined type-hint string
     *
     * Handles named types, union types, and intersection types -- including a DNF union whose
     * members are themselves intersection types (e.g. `(A&B)|C`), which PHP 8.2+ allows. This
     * consolidates what used to be four separate, partially-duplicated, partially-incomplete
     * copies of this logic (MethodReflection and FunctionReflection each had their own -- neither
     * handled intersection types, and MethodReflection's parameter branch and FunctionReflection's
     * parameter branch didn't handle unions at all, either crashing or silently dropping the type).
     *
     * @param  ?\ReflectionType $type
     * @return string|null
     */
    public static function resolveReflectionType(?\ReflectionType $type): string|null
    {
        if ($type === null) {
            return null;
        }

        if ($type instanceof \ReflectionIntersectionType) {
            return self::joinIntersectionType($type);
        }

        if ($type instanceof \ReflectionUnionType) {
            $names = [];
            foreach ($type->getTypes() as $memberType) {
                $names[] = ($memberType instanceof \ReflectionIntersectionType)
                    ? self::joinIntersectionType($memberType)
                    : $memberType->getName();
            }
            if ($type->allowsNull() && !in_array('null', $names, true)) {
                $names[] = 'null';
            }
            return implode('|', $names);
        }

        if ($type instanceof \ReflectionNamedType) {
            $name = $type->getName();
            if (($name !== 'mixed') && ($name !== 'null') && $type->allowsNull()) {
                $name .= '|null';
            }
            return $name;
        }

        return null;
    }

    /**
     * Join an intersection type's member names with '&'
     *
     * @param  \ReflectionIntersectionType $type
     * @return string
     */
    protected static function joinIntersectionType(\ReflectionIntersectionType $type): string
    {
        $names = [];
        foreach ($type->getTypes() as $memberType) {
            $names[] = $memberType->getName();
        }
        return implode('&', $names);
    }

}
