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
namespace Pop\Code\Generator\Support;

use Pop\Code\Generator\Exception;
use Pop\Code\Generator\Literal;

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
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class ValueFormatter
{

    /**
     * Format a value as PHP literal source (no trailing semicolon)
     *
     * @param  mixed   $value
     * @param  ?string $type
     * @param  string  $indent
     * @param  bool    $compact  render an array value on a single line instead of the default
     *                           multi-line bracket-literal form -- used for attribute arguments,
     *                           which always render inline (even a top-level `#[...]` is
     *                           conventionally one physical line) and would otherwise force a
     *                           multi-line array literal into the middle of a parameter list
     * @return string
     */
    public static function format(mixed $value, ?string $type = null, string $indent = '', bool $compact = false): string
    {
        if ($value === null) {
            return 'null';
        }

        // A Literal wraps a raw PHP-source expression (e.g. 'self::FOO') that must be emitted
        // verbatim rather than quoted/escaped -- must be checked before the object/Stringable
        // check below, since Literal itself has no __toString() and would otherwise incorrectly
        // hit that exception path.
        if ($value instanceof Literal) {
            return $value->getValue();
        }

        // Enum cases (e.g. a class constant like `const DEFAULT = self::Active;`) aren't
        // Stringable, but they do have a well-defined literal form: <ShortClassName>::<CaseName>.
        // Using the short name (not an FQCN) matches how the rest of this codebase renders
        // in-namespace type references, and is always valid PHP when the value is a case of the
        // enum being rendered, since that reference lands back in the same namespace.
        if ($value instanceof \UnitEnum) {
            return (new \ReflectionClass($value))->getShortName() . '::' . $value->name;
        }

        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new Exception('Error: Cannot format an object value of type ' . get_class($value) . '.');
        }

        $effectiveType = $type ?? strtolower(gettype($value));

        // A union type string (e.g. 'int|string') doesn't match any of the single-type checks
        // below, so it fell through to the catch-all string-quoting branch regardless of the
        // value's actual type -- silently coercing e.g. an int|string default that's really an
        // int into a quoted string literal. Pick the union member matching the value's actual
        // PHP type, if present, so formatting proceeds as if that were the declared type.
        if (str_contains($effectiveType, '|')) {
            $actualType = strtolower(gettype($value));
            $actualType = match ($actualType) {
                'integer' => 'int',
                'double'  => 'float',
                'boolean' => 'bool',
                default   => $actualType,
            };
            $members = explode('|', $effectiveType);
            if (in_array($actualType, $members, true)) {
                $effectiveType = $actualType;
            }
        }

        if ($effectiveType === 'array') {
            if (count($value) === 0) {
                return '[]';
            }
            return $compact ? self::formatArrayCompact($value) : self::formatArray($value, $indent);
        }

        if (in_array($effectiveType, ['int', 'integer', 'float', 'double'], true)) {
            return (string) $value;
        }

        if (in_array($effectiveType, ['bool', 'boolean'], true)) {
            return $value ? 'true' : 'false';
        }

        return "'" . addcslashes((string) $value, "'\\") . "'";
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

    /**
     * Format a non-empty array value as a single-line PHP bracket-literal source
     *
     * @param  array $value
     * @return string
     */
    protected static function formatArrayCompact(array $value): string
    {
        $keys    = array_keys($value);
        $isAssoc = false;

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $i) {
                $isAssoc = true;
            }
        }

        $parts = [];
        foreach ($value as $key => $item) {
            $formattedItem = self::format($item, null, '', true);
            $parts[]       = $isAssoc ? var_export($key, true) . ' => ' . $formattedItem : $formattedItem;
        }

        return '[' . implode(', ', $parts) . ']';
    }

}
