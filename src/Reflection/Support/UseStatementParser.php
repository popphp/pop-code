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
 * Use-statement parser class
 *
 * Scrapes `use Foo\Bar;` / `use Foo\Bar as Baz;` lines out of raw PHP source text. Native reflection has
 * no API for "which traits/classes does this file `use`," so ClassReflection and TraitReflection both
 * need this; it previously lived as an identical copy of this regex in each of them.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class UseStatementParser
{

    /**
     * Parse `use` statements out of raw PHP source
     *
     * @param  string $sourceCode
     * @return array
     */
    public static function parse(string $sourceCode): array
    {
        $result  = [];
        $matches = [];

        preg_match_all('/[ ]+use(.*);$/m', $sourceCode, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $u) {
                $useAry = array_map('trim', explode(',', trim($u)));
                foreach ($useAry as $useValue) {
                    if (strpos($useValue, ' as ') !== false) {
                        [$use, $as] = explode(' as ', $useValue);
                    } else {
                        $use = $useValue;
                        $as  = null;
                    }
                    $result[trim($use)] = ($as !== null) ? trim($as) : null;
                }
            }
        }

        return $result;
    }

}
