<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection;

use Pop\Code\Generator\NamespaceGenerator;

/**
 * Namespace reflection code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class NamespaceReflection extends AbstractReflection
{

    /**
     * Method to parse a namespace
     *
     * @param  mixed   $code
     * @param  ?string $name
     * @throws Exception
     * @return NamespaceGenerator
     */
    public static function parse(mixed $code, ?string $name = null): NamespaceGenerator
    {
        if ($name === null) {
            $matches = [];
            preg_match_all('/^namespace(.*);$/m', $code, $matches);
            if (isset($matches[1]) && isset($matches[1][0])) {
                $name = $matches[1][0];
            }
        }

        if (empty($name)) {
            throw new Exception('Error: The namespace name has not been set.');
        }

        $namespace = new NamespaceGenerator($name);
        $matches   = [];

        preg_match_all('/^use(.*);$/m', $code, $matches);

        if (isset($matches[1]) && isset($matches[1][0])) {
            foreach ($matches[1] as $match) {
                if (str_contains($match, ' as ')) {
                    [$use, $as] = explode(' as ', $match);
                } else {
                    $use = $match;
                    $as  = null;
                }
                $namespace->addUse($use, $as);
            }
        }

        return $namespace;
    }

}