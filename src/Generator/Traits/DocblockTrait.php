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

use Pop\Code\Generator\DocblockGenerator;

/**
 * Docblock trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
trait DocblockTrait
{

    /**
     * Docblock generator object
     * @var ?DocblockGenerator
     */
    protected ?DocblockGenerator $docblock = null;

    /**
     * Set the docblock generator object
     *
     * @param  DocblockGenerator $docblock
     * @return static
     */
    public function setDocblock(DocblockGenerator $docblock): static
    {
        $this->docblock = $docblock;
        return $this;
    }

    /**
     * Access the docblock generator object
     *
     * @return DocblockGenerator|null
     */
    public function getDocblock(): DocblockGenerator|null
    {
        return $this->docblock;
    }

    /**
     * Has docblock generator object
     *
     * @return bool
     */
    public function hasDocblock(): bool
    {
        return ($this->docblock !== null);
    }

    /**
     * Set the docblock description
     *
     * @param  ?string $desc
     * @return static
     */
    public function setDesc(?string $desc = null): static
    {
        if ($this->docblock !== null) {
            $this->docblock->setDesc($desc);
        } else {
            $this->docblock = new DocblockGenerator($desc);
        }
        return $this;
    }

    /**
     * Get the docblock description
     *
     * @return string|null
     */
    public function getDesc(): string|null
    {
        return ($this->docblock !== null) ? $this->docblock->getDesc() : null;
    }

    /**
     * Has a docblock description
     *
     * @return bool
     */
    public function hasDesc(): bool
    {
        return ($this->docblock !== null) ? $this->docblock->hasDesc() : false;
    }

}
