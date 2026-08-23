<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Reflection\Support;

/**
 * Interface hierarchy resolver class
 *
 * Shared by ClassReflection and InterfaceReflection, which previously each carried their own
 * identical copy of this filter.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class InterfaceHierarchyResolver
{

    /**
     * Filter a ReflectionClass::getInterfaces() result down to the interfaces directly declared
     * here -- getInterfaces() returns the full transitive closure, so a candidate is excluded if
     * it's already reachable via $excludeNames (e.g. a parent class's own interfaces) or is
     * implied by another candidate already in the set (i.e. reported by that candidate's own
     * getInterfaceNames(), meaning it isn't a distinct direct declaration here).
     *
     * @param  array<string, \ReflectionClass> $interfaces
     * @param  array<int, string>              $excludeNames
     * @return array<string, \ReflectionClass>
     */
    public static function direct(array $interfaces, array $excludeNames = []): array
    {
        // getInterfaceNames() is called once per candidate here, not once per candidate per
        // candidate -- the transitivity check below needs every candidate's own interface list,
        // not just the one currently being tested.
        $interfaceNamesByCandidate = [];
        foreach ($interfaces as $otherName => $other) {
            $interfaceNamesByCandidate[$otherName] = $other->getInterfaceNames();
        }

        $direct = [];

        foreach ($interfaces as $candidateName => $candidate) {
            if (in_array($candidateName, $excludeNames, true)) {
                continue;
            }

            $isTransitive = false;
            foreach ($interfaceNamesByCandidate as $otherName => $otherInterfaceNames) {
                if (($otherName !== $candidateName) && in_array($candidateName, $otherInterfaceNames, true)) {
                    $isTransitive = true;
                    break;
                }
            }

            if (!$isTransitive) {
                $direct[$candidateName] = $candidate;
            }
        }

        return $direct;
    }

}
