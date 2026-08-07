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

use Pop\Code\Generator\AttributeGenerator;

/**
 * Attribute collector class
 *
 * Builds an AttributeGenerator from a \ReflectionAttribute. Deliberately does not attempt any
 * `use`-import wiring for the attribute's own class — only a top-level *Reflection::parse() call has
 * direct access to the construct's namespace object, so that stays inline in each caller. A caller
 * that does have that access (and a Reflection\Support\NamespaceImportResolver to go with it, for
 * same-short-name collision avoidance) can override the computed name via $name.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class AttributeCollector
{

    /**
     * Build an AttributeGenerator from a ReflectionAttribute
     *
     * @param  \ReflectionAttribute $reflectionAttribute
     * @param  ?string              $name  overrides the short name this would otherwise compute
     * @return AttributeGenerator
     */
    public static function build(\ReflectionAttribute $reflectionAttribute, ?string $name = null): AttributeGenerator
    {
        if ($name === null) {
            $fqcn  = $reflectionAttribute->getName();
            $parts = explode('\\', $fqcn);
            $name  = str_contains($fqcn, '\\') ? end($parts) : '\\' . $fqcn;
        }

        $attribute = new AttributeGenerator($name);

        foreach ($reflectionAttribute->getArguments() as $key => $value) {
            if (is_string($key)) {
                $attribute->addArgument($value, $key);
            } else {
                $attribute->addArgument($value);
            }
        }

        return $attribute;
    }

}
