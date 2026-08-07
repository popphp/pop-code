<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator;

use Pop\Code\Generator\Support\ValueFormatter;

/**
 * Attribute generator class
 *
 * Represents one `#[Name(args)]` usage. Deliberately never calls printIndent() — its correct
 * indentation depends entirely on where it's used (none for a class-level attribute, matching the
 * host's own indent for a member-level one), a decision made by the caller (AttributesTrait), not by
 * this class.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class AttributeGenerator extends AbstractGenerator
{

    use Traits\NameTrait;

    /**
     * Arguments, each shaped ['name' => ?string, 'value' => mixed]
     * @var array
     */
    protected array $arguments = [];

    /**
     * Constructor
     *
     * Instantiate the attribute generator object
     *
     * @param  string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Add an argument
     *
     * Parameter order is (value, name), not (name, value) like Traits\FunctionTrait::addArgument()
     * elsewhere in this library — deliberate, since it mirrors ReflectionAttribute::getArguments()'s
     * own shape (numeric keys positional, string keys named) and makes the common positional case a
     * single-argument call: addArgument('value') rather than addArgument(null, 'value').
     *
     * @param  mixed   $value
     * @param  ?string $name
     * @return AttributeGenerator
     */
    public function addArgument(mixed $value, ?string $name = null): AttributeGenerator
    {
        $this->arguments[] = ['name' => $name, 'value' => $value];
        return $this;
    }

    /**
     * Has arguments
     *
     * @return bool
     */
    public function hasArguments(): bool
    {
        return !empty($this->arguments);
    }

    /**
     * Get the arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Render attribute
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = '#[' . $this->name;

        if (!empty($this->arguments)) {
            $formatted = [];
            foreach ($this->arguments as $argument) {
                $value       = ValueFormatter::format($argument['value'], null, '', true);
                $formatted[] = ($argument['name'] !== null) ? $argument['name'] . ': ' . $value : $value;
            }
            $this->output .= '(' . implode(', ', $formatted) . ')';
        }

        $this->output .= ']';

        return $this->output;
    }

    /**
     * Print attribute
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
