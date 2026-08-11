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
 * Namespace import resolver class
 *
 * Tracks which short class names have already been claimed by a `use`-import within one
 * *Reflection::parse() call, and decides -- for each new fully-qualified class name a parent
 * class, interface, or attribute needs to reference -- whether it can be imported (and rendered
 * by its bare short name) or must be referenced by its fully-qualified name instead, because
 * another class with the same short name from a *different* namespace already claimed it. Two
 * `use` statements for different classes sharing one short name is a PHP fatal error ("Cannot use
 * X as Y because the name is already in use"), so the second one must fall back to its FQCN
 * instead of colliding.
 *
 * One instance is meant to live for the duration of a single *Reflection::parse() call, shared
 * across every parent/interface/attribute reference that call detects, so a collision between
 * (for example) the parent class's short name and an attribute's short name is caught too, not
 * just collisions among attributes alone.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class NamespaceImportResolver
{

    /**
     * Short names already claimed by this resolver, each mapped to the FQCN that claimed it
     * @var array
     */
    protected array $claimed = [];

    /**
     * Resolve a fully-qualified class name into [reference, needsImport]
     *
     * `$reference` is what should be rendered in place of the class name (a bare short name, or
     * a fully-qualified name with a leading backslash). `$needsImport` is whether the caller
     * should add a `use $fqcn;` import for it.
     *
     * @param  string $fqcn
     * @param  string $ownNamespace  the namespace of the construct being reflected
     * @return array
     */
    public function resolve(string $fqcn, string $ownNamespace): array
    {
        $lastSlash = strrpos($fqcn, '\\');

        if ($lastSlash === false) {
            // A genuinely root-namespace class (no backslash at all) is always unambiguous with
            // a leading backslash, regardless of where it's referenced from -- it never needs an
            // import and can never collide with one, unlike a namespaced class whose bare short
            // name could shadow another import.
            return ['\\' . $fqcn, false];
        }

        $namespacePart = substr($fqcn, 0, $lastSlash);
        $short         = substr($fqcn, $lastSlash + 1);

        if ($namespacePart === $ownNamespace) {
            return [$short, false];
        }

        if (isset($this->claimed[$short])) {
            return ($this->claimed[$short] === $fqcn) ? [$short, false] : ['\\' . $fqcn, false];
        }

        $this->claimed[$short] = $fqcn;

        return [$short, true];
    }

}
