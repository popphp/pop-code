<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2021 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2021 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
trait UseTrait
{

    /**
     * Array of namespaces to use
     * @var array
     */
    protected $uses = [];

    /**
     * Add a namespace to use
     *
     * @param  string $use
     * @param  string $as
     * @return UseTrait
     */
    public function addUse($use, $as = null)
    {
        $this->uses[$use] = $as;
        return $this;
    }

    /**
     * Add namespaces to use
     *
     * @param  array $uses
     * @return UseTrait
     */
    public function addUses(array $uses)
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
     * @return boolean
     */
    public function hasUse($use)
    {
        return (array_key_exists($use, $this->uses));
    }

    /**
     * Has uses
     *
     * @return boolean
     */
    public function hasUses()
    {
        return (!empty($this->uses));
    }

    /**
     * Get uses
     *
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

}