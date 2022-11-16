<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
trait DocblockTrait
{

    /**
     * Docblock generator object
     * @var DocblockGenerator
     */
    protected $docblock = null;

    /**
     * Set the docblock generator object
     *
     * @param  DocblockGenerator $docblock
     * @return DocblockTrait
     */
    public function setDocblock(DocblockGenerator $docblock)
    {
        $this->docblock = $docblock;
        return $this;
    }

    /**
     * Access the docblock generator object
     *
     * @return DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    /**
     * Has docblock generator object
     *
     * @return boolean
     */
    public function hasDocblock()
    {
        return (null !== $this->docblock);
    }

    /**
     * Set the docblock description
     *
     * @param  string $desc
     * @return DocblockTrait
     */
    public function setDesc($desc = null)
    {
        if (null !== $this->docblock) {
            $this->docblock->setDesc($desc);
        } else {
            $this->docblock = new DocblockGenerator($desc);
        }
        return $this;
    }

    /**
     * Get the docblock description
     *
     * @return string
     */
    public function getDesc()
    {
        return (null !== $this->docblock) ? $this->docblock->getDesc() : null;
    }

    /**
     * Has a docblock description
     *
     * @return boolean
     */
    public function hasDesc()
    {
        return (null !== $this->docblock) ? $this->docblock->hasDesc() : false;
    }

}