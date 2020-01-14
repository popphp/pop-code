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

    use Traits\NameTrait, Traits\DocblockTrait;

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
     * Set the visibility
     *
     * @param  string $visibility
     * @return AbstractClassElementGenerator
     */
    public function setVisibility($visibility = 'public')
    {
        $visibility = strtolower($visibility);

        if (!in_array($visibility, ['public', 'protected', 'private'])) {
            throw new Exception("Error: The visibility '" . $visibility . "' is not allowed.");
        }

        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Set the visibility to public
     *
     * @return AbstractClassElementGenerator
     */
    public function setAsPublic()
    {
        return $this->setVisibility('public');
    }

    /**
     * Set the visibility to protected
     *
     * @return AbstractClassElementGenerator
     */
    public function setAsProtected()
    {
        return $this->setVisibility('protected');
    }

    /**
     * Set the visibility to public
     *
     * @return AbstractClassElementGenerator
     */
    public function setAsPrivate()
    {
        return $this->setVisibility('private');
    }

    /**
     * Is visibility public
     *
     * @return boolean
     */
    public function isPublic()
    {
        return ($this->visibility == 'public');
    }

    /**
     * Set the visibility to protected
     *
     * @return boolean
     */
    public function isProtected()
    {
        return ($this->visibility == 'protected');
    }

    /**
     * Set the visibility to private
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return ($this->visibility == 'private');
    }

    /**
     * Get the visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set the static flag
     *
     * @param  boolean $static
     * @return AbstractClassElementGenerator
     */
    public function setAsStatic($static = true)
    {
        $this->static = (boolean)$static;
        return $this;
    }

    /**
     * Get the static flag
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->static;
    }

}