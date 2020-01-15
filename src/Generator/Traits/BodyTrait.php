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
     * @return BodyTrait
     */
    public function setBody($body)
    {
        $this->body = $this->printIndent() . '    ' .  str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  string $body
     * @return BodyTrait
     */
    public function appendToBody($body)
    {
        $body = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        $this->body .= PHP_EOL . $this->printIndent() . '    ' . $body;
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  int $indent
     * @return BodyTrait
     */
    public function indentBody($indent)
    {
        $indent = (int)$indent;

        if ($indent > 0) {
            $this->body = str_repeat(' ', $indent) . str_replace(PHP_EOL, PHP_EOL . str_repeat(' ', $indent), $this->body);
        } else if ($indent < 0) {
            $indent    = abs($indent);
            $bodyLines = explode(PHP_EOL, $this->body);
            foreach ($bodyLines as $i => $bodyLine) {
                if (substr($bodyLine, 0, $indent) == str_repeat(' ', $indent)) {
                    $bodyLines[$i] = substr($bodyLine, $indent);
                }
            }
            $this->body = implode(PHP_EOL, $bodyLines);
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