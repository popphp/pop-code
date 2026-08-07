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
 * Constant reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class ConstantReflection extends AbstractReflection
{

    /**
     * Method to parse a constant
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @return Generator\ConstantGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\ConstantGenerator
    {
        if ($code->isProtected()) {
            $visibility = 'protected';
        } else if ($code->isPrivate()) {
            $visibility = 'private';
        } else {
            $visibility = 'public';
        }

        $isTyped = $code->hasType();
        $type    = $isTyped ? (string) $code->getType() : TypeNormalizer::normalize(strtolower(gettype($code->getValue())));

        $constant = new Generator\ConstantGenerator($name ?? $code->getName(), $type, $code->getValue());
        $constant->setVisibility($visibility);
        $constant->setTyped($isTyped);

        foreach ($code->getAttributes() as $reflectionAttribute) {
            $constant->addAttribute(AttributeCollector::build($reflectionAttribute));
        }

        return $constant;
    }

}
