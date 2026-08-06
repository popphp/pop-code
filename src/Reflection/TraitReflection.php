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
use Pop\Code\Reflection\Support\UseStatementParser;
use ReflectionException;

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
class TraitReflection extends AbstractReflection
{

    /**
     * Method to parse a trait
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @throws Exception|ReflectionException
     * @return Generator\TraitGenerator
     */
    public static function parse(mixed $code, ?string $name = null): Generator\TraitGenerator
    {
        $reflection     = new \ReflectionClass($code);
        $reflectionName = $reflection->getShortName();
        $reflectionFile = $reflection->getFileName();
        $fileContents   = null;

        if (!empty($reflectionFile) && file_exists($reflectionFile)) {
            $fileContents = file_get_contents($reflectionFile);
        }

        if (($name === null) && !empty($reflectionName)) {
            $name = $reflectionName;
        }

        if (!$reflection->isTrait()) {
            throw new Exception('Error: The code is not a trait.');
        }

        $trait = new Generator\TraitGenerator($name);

        // Detect and set namespace
        if (($reflection->inNamespace()) && ($fileContents !== null)) {
            $trait->setNamespace(NamespaceReflection::parse($fileContents, $reflection->getNamespaceName()));
        }

        // Detect and set the class doc block
        $traitDocBlock = $reflection->getDocComment();
        if (!empty($traitDocBlock) && (str_contains($traitDocBlock, '/*'))) {
            $trait->setDocblock(DocblockReflection::parse($traitDocBlock));
        }

        // Detect used traits
        if ($fileContents !== null) {
            foreach (UseStatementParser::parse($fileContents) as $use => $as) {
                $trait->addUse($use, $as);
            }
        }

        // Detect properties
        foreach ($reflection->getProperties() as $property) {
            $value = $property->hasDefaultValue() ? $property->getDefaultValue() : null;
            $trait->addProperty(PropertyReflection::parse($property, $property->getName(), $value));
        }

        // Detect methods
        $methods = $reflection->getMethods();
        if (count($methods) > 0) {
            foreach ($methods as $method) {
                $trait->addMethod(MethodReflection::parse($method, $method->name));
            }
        }

        return $trait;
    }

}
