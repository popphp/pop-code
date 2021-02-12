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
 * Abstract final trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2021 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
trait AbstractFinalTrait
{

    /**
     * Method abstract flag
     * @var boolean
     */
    protected $abstract = false;

    /**
     * Method final flag
     * @var boolean
     */
    protected $final = false;

    /**
     * Set the method abstract flag
     *
     * @param  boolean $abstract
     * @return AbstractFinalTrait
     */
    public function setAsAbstract($abstract = true)
    {
        $this->abstract = (boolean)$abstract;
        if ($this->abstract) {
            $this->setAsFinal(false);
        }
        return $this;
    }

    /**
     * Get the method abstract flag
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set the method final flag
     *
     * @param  boolean $final
     * @return AbstractFinalTrait
     */
    public function setAsFinal($final = true)
    {
        $this->final = (boolean)$final;
        if ($this->final) {
            $this->setAsAbstract(false);
        }
        return $this;
    }

    /**
     * Get the method final flag
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

}