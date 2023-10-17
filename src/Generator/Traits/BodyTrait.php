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
 * Body trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait BodyTrait
{

    /**
     * Function body
     * @var ?string
     */
    protected ?string $body = null;

    /**
     * Set the function body
     *
     * @param  string $body
     * @param  int    $indent
     * @return static
     */
    public function setBody(string $body, int $indent = 4): static
    {
        $this->body = $this->printIndent() . str_repeat(' ', $indent) .
            str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . str_repeat(' ', $indent), $body);
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  string $body
     * @return static
     */
    public function appendToBody(string $body): static
    {
        $body = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        $this->body .= PHP_EOL . $this->printIndent() . '    ' . $body;
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  int $indent
     * @return static
     */
    public function indentBody(int $indent): static
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
     * @return string|null
     */
    public function getBody(): string|null
    {
        return $this->body;
    }

    /**
     * Has method body
     *
     * @return bool
     */
    public function hasBody(): bool
    {
        return ($this->body !== null);
    }

}