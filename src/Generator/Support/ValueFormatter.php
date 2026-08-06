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
namespace Pop\Code\Generator\Support;

/**
 * Value formatter class
 *
 * Turns a real PHP value into its PHP literal source form. Shared by PropertyGenerator,
 * ConstantGenerator, and FunctionTrait's argument-default formatting so all three format values the
 * same way instead of each carrying its own (previously divergent) copy of this logic.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class ValueFormatter
{

    /**
     * Format a value as PHP literal source (no trailing semicolon)
     *
     * @param  mixed   $value
     * @param  ?string $type
     * @param  string  $indent
     * @return string
     */
    public static function format(mixed $value, ?string $type = null, string $indent = ''): string
    {
        if ($value === null) {
            return 'null';
        }

        $effectiveType = $type ?? strtolower(gettype($value));

        if ($effectiveType === 'array') {
            return (count($value) === 0) ? '[]' : self::formatArray($value, $indent);
        }

        if (in_array($effectiveType, ['int', 'integer', 'float'], true)) {
            return (string) $value;
        }

        if (in_array($effectiveType, ['bool', 'boolean'], true)) {
            return $value ? 'true' : 'false';
        }

        return "'" . $value . "'";
    }

    /**
     * Format an array value as PHP bracket-literal source
     *
     * @param  array  $value
     * @param  string $indent
     * @return string
     */
    protected static function formatArray(array $value, string $indent): string
    {
        $ary = str_replace(PHP_EOL, PHP_EOL . $indent . '  ', var_export($value, true));
        $ary = str_replace('array (', '[', $ary);
        $ary = str_replace('  )', ']', $ary);
        $ary = str_replace('NULL', 'null', $ary);

        $keys    = array_keys($value);
        $isAssoc = false;

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $i) {
                $isAssoc = true;
            }
        }

        if (!$isAssoc) {
            for ($i = 0; $i < count($keys); $i++) {
                $ary = str_replace($i . ' => ', '', $ary);
            }
        }

        return $ary;
    }

}
