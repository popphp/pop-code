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
 * Type normalizer class
 *
 * Maps gettype()'s legacy type names to the PHP type-hint keyword they should have been. gettype()
 * returns 'integer'/'boolean'/'double', none of which are valid in a PHP type-hint position — using
 * them there directly is what produced invalid generated code before this fix.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
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

}
