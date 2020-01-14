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
namespace Pop\Code\Generator\Traits;

/**
 * Body trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
trait BodyTrait
{

    /**
     * Function body
     * @var string
     */
    protected $body = null;

    /**
     * Set the function body
     *
     * @param  string $body
     * @param  boolean $newline
     * @return BodyTrait
     */
    public function setBody($body, $newline = true)
    {
        $this->body = $this->printIndent() . '    ' .  str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  string  $body
     * @param  boolean $newline
     * @return BodyTrait
     */
    public function appendToBody($body, $newline = true)
    {
        $body = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        $this->body .= $this->printIndent() . '    ' . $body;
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Get the function body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Has method body
     *
     * @return boolean
     */
    public function hasBody()
    {
        return (null !== $this->body);
    }

}