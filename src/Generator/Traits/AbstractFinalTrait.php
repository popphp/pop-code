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
namespace Pop\Code\Generator\Traits;

/**
 * Abstract final trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait AbstractFinalTrait
{

    /**
     * Method abstract flag
     * @var bool
     */
    protected bool $abstract = false;

    /**
     * Method final flag
     * @var bool
     */
    protected bool $final = false;

    /**
     * Set the method abstract flag
     *
     * @param  bool $abstract
     * @return static
     */
    public function setAsAbstract(bool $abstract = true): static
    {
        $this->abstract = $abstract;
        if ($this->abstract) {
            $this->setAsFinal(false);
        }
        return $this;
    }

    /**
     * Get the method abstract flag
     *
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * Set the method final flag
     *
     * @param  bool $final
     * @return static
     */
    public function setAsFinal(bool $final = true): static
    {
        $this->final = $final;
        if ($this->final) {
            $this->setAsAbstract(false);
        }
        return $this;
    }

    /**
     * Get the method final flag
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

}