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
 * Docblock trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
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

}