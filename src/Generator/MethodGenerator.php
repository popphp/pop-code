<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
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
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
class MethodGenerator extends AbstractClassElementGenerator
{

    use Traits\AbstractFinalTrait, Traits\FunctionTrait, Traits\BodyTrait;

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
     * Add a promoted constructor argument
     *
     * @param  string  $name
     * @param  string  $visibility
     * @param  mixed   $value
     * @param  ?string $type
     * @param  bool    $readonly
     * @param  array   $attributes
     * @throws Exception
     * @return MethodGenerator
     */
    public function addPromotedArgument(
        string $name, string $visibility, mixed $value = new NoValue(), ?string $type = null, bool $readonly = false,
        array $attributes = []
    ): MethodGenerator
    {
        if ($this->name !== '__construct') {
            throw new Exception('Error: Promoted arguments are only valid on a constructor.');
        }

        $visibility = strtolower($visibility);
        if (!in_array($visibility, self::VALID_VISIBILITIES)) {
            throw new Exception("Error: The visibility '" . $visibility . "' is not allowed.");
        }

        if ($readonly && ($type === null)) {
            throw new Exception('Error: A readonly property must have a type.');
        }

        $this->addArgument($name, $value, $type, false, false, $attributes);
        $this->arguments[$name]['promotedVisibility'] = $visibility;
        $this->arguments[$name]['promotedReadonly']   = $readonly;

        return $this;
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
        $this->output .= $this->formatAttributes();
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
