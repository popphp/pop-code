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
 * direct access to the construct's namespace object, so that stays inline in each caller.
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
     * @return AttributeGenerator
     */
    public static function build(\ReflectionAttribute $reflectionAttribute): AttributeGenerator
    {
        $name  = $reflectionAttribute->getName();
        $parts = explode('\\', $name);
        $short = str_contains($name, '\\') ? end($parts) : '\\' . $name;

        $attribute = new AttributeGenerator($short);

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
