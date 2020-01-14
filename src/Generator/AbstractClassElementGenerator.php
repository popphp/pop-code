<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator;

/**
 * Abstract class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
abstract class AbstractClassElementGenerator extends AbstractGenerator
{

    use NameTrait, DocblockTrait;

    /**
     * Visibility
     * @var string
     */
    protected $visibility = 'public';

    /**
     * Static flag
     * @var boolean
     */
    protected $static = false;

    /**
     * Set the property visibility
     *
     * @param  string $visibility
     * @return AbstractClassElementGenerator
     */
    public function setVisibility($visibility = 'public')
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Get the property visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set the property static flag
     *
     * @param  boolean $static
     * @return AbstractClassElementGenerator
     */
    public function setStatic($static = false)
    {
        $this->static = (boolean)$static;
        return $this;
    }

    /**
     * Get the property static flag
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->static;
    }

}