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
 * Namespace generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
class MethodGenerator extends AbstractClassElementGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\AbstractFinalTrait, Traits\FunctionTrait, Traits\BodyTrait;

    /**
     * Method body
     * @var ?string string
     */
    protected ?string $body = null;

    /**
     * Constructor
     *
     * Instantiate the method generator object
     *
     * @param  string $name
     * @param  string $visibility
     * @param  bool $static
     * @throws Exception
     */
    public function __construct(string $name, string $visibility = 'public', bool $static = false)
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
    public function render(): string
    {
        $final    = ($this->final) ? 'final ' : null;
        $abstract = ($this->abstract) ? 'abstract ' : null;
        $static   = ($this->static) ? ' static' : null;
        $args     = $this->formatArguments();

        $this->output = PHP_EOL . (($this->docblock !== null) ? $this->docblock->render() : null);
        $this->output .= $this->printIndent() . $final . $abstract . $this->visibility .
            $static . ' function ' . $this->name . '(' . $args . ')';

        if ($this->hasReturnTypes()) {
            $this->output .= ': ' . implode('|', $this->returnTypes);
        }

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
    public function __toString(): string
    {
        return $this->render();
    }

}
