<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator\Traits;

/**
 * Use trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait UseTrait
{

    /**
     * Array of namespaces to use
     * @var array
     */
    protected array $uses = [];

    /**
     * Add a namespace to use
     *
     * @param  string  $use
     * @param  ?string $as
     * @return static
     */
    public function addUse(string $use, ?string $as = null): static
    {
        $this->uses[$use] = $as;
        return $this;
    }

    /**
     * Add namespaces to use
     *
     * @param  array $uses
     * @return static
     */
    public function addUses(array $uses): static
    {
        foreach ($uses as $as => $use) {
            if (!is_numeric($as)) {
                $this->addUse($use, $as);
            } else {
                $this->addUse($use);
            }
        }
        return $this;
    }

    /**
     * Has use
     *
     * @param  string $use
     * @return bool
     */
    public function hasUse(string$use): bool
    {
        return array_key_exists($use, $this->uses);
    }

    /**
     * Has uses
     *
     * @return bool
     */
    public function hasUses(): bool
    {
        return (!empty($this->uses));
    }

    /**
     * Get uses
     *
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

}
