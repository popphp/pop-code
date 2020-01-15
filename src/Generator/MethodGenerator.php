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
 * Namespace generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class MethodGenerator extends AbstractClassElementGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\AbstractFinalTrait, Traits\FunctionTrait, Traits\BodyTrait;

    /**
     * Method body
     * @var string
     */
    protected $body = null;

    /**
     * Constructor
     *
     * Instantiate the method generator object
     *
     * @param  string  $name
     * @param  string  $visibility
     * @param  boolean $static
     */
    public function __construct($name, $visibility = 'public', $static = false)
    {
        $this->setName($name);
        $this->setVisibility($visibility);
        $this->setAsStatic($static);
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $final    = ($this->final) ? 'final ' : null;
        $abstract = ($this->abstract) ? 'abstract ' : null;
        $static   = ($this->static) ? ' static' : null;
        $args     = $this->formatArguments();

        $this->output = PHP_EOL . ((null !== $this->docblock) ? $this->docblock->render() : null);
        $this->output .= $this->printIndent() . $final . $abstract . $this->visibility .
            $static . ' function ' . $this->name . '(' . $args . ')';

        if (!empty($this->body)) {
            $this->output .= PHP_EOL . $this->printIndent() . '{' . PHP_EOL;
            $this->output .= $this->body. PHP_EOL;
            $this->output .= $this->printIndent() . '}';
        } else {
            $this->output .= ';';
        }

        return $this->output;
    }

    /**
     * Print method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}