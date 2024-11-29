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
namespace Pop\Code\Generator;

/**
 * Abstract class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
abstract class AbstractClassElementGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait;

    /**
     * Visibility
     * @var string
     */
    protected string $visibility = 'public';

    /**
     * Static flag
     * @var bool
     */
    protected bool $static = false;

    /**
     * Set the visibility
     *
     * @param  string $visibility
     * @throws Exception
     * @return AbstractClassElementGenerator
     */
    public function setVisibility(string $visibility = 'public'): AbstractClassElementGenerator
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
     * @throws Exception
     * @return AbstractClassElementGenerator
     */
    public function setAsPublic(): AbstractClassElementGenerator
    {
        return $this->setVisibility('public');
    }

    /**
     * Set the visibility to protected
     *
     * @throws Exception
     * @return AbstractClassElementGenerator
     */
    public function setAsProtected(): AbstractClassElementGenerator
    {
        return $this->setVisibility('protected');
    }

    /**
     * Set the visibility to public
     *
     * @throws Exception
     * @return AbstractClassElementGenerator
     */
    public function setAsPrivate(): AbstractClassElementGenerator
    {
        return $this->setVisibility('private');
    }

    /**
     * Is visibility public
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return ($this->visibility == 'public');
    }

    /**
     * Set the visibility to protected
     *
     * @return bool
     */
    public function isProtected(): bool
    {
        return ($this->visibility == 'protected');
    }

    /**
     * Set the visibility to private
     *
     * @return bool
     */
    public function isPrivate(): bool
    {
        return ($this->visibility == 'private');
    }

    /**
     * Get the visibility
     *
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * Set the static flag
     *
     * @param  bool $static
     * @return AbstractClassElementGenerator
     */
    public function setAsStatic(bool $static = true): AbstractClassElementGenerator
    {
        $this->static = $static;
        return $this;
    }

    /**
     * Get the static flag
     *
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

}
