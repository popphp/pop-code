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
 * Constant generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class ConstantGenerator extends AbstractClassElementGenerator
{

    /**
     * Constant type
     * @var ?string
     */
    protected ?string $type = null;

    /**
     * Constant value
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * Typed-signature flag: when true and a type is set, render() emits the type in the signature
     * @var bool
     */
    protected bool $typed = false;

    /**
     * Constructor
     *
     * Instantiate the constant generator object
     *
     * @param  string $name
     * @param  string $type
     * @param  mixed  $value
     */
    public function __construct(string $name, string $type, mixed $value = null)
    {
        $this->setName($name);
        $this->setType($type);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Set the constant type
     *
     * @param  string $type
     * @return ConstantGenerator
     */
    public function setType(string $type): ConstantGenerator
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the constant type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the constant value
     *
     * @param  mixed $value
     * @return ConstantGenerator
     */
    public function setValue(mixed $value = null): ConstantGenerator
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the constant value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Has constant value
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    /**
     * Set whether the declared type is rendered in the signature (PHP 8.3+ typed constants)
     *
     * @param  bool $typed
     * @return ConstantGenerator
     */
    public function setTyped(bool $typed = true): ConstantGenerator
    {
        $this->typed = $typed;
        return $this;
    }

    /**
     * Determine if the declared type is rendered in the signature
     *
     * @return bool
     */
    public function isTyped(): bool
    {
        return $this->typed;
    }

    /**
     * Render constant
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        $this->docblock->addTag('var', $this->type);
        $this->output  = PHP_EOL . $this->docblock->render();
        $this->output .= $this->formatAttributes();
        $this->output .= $this->printIndent() . $this->visibility . ' const';

        if ($this->typed && ($this->type !== null)) {
            $this->output .= ' ' . $this->type;
        }

        $this->output .= ' ' . $this->name;

        if (($this->value === null) && ($this->type === 'array')) {
            $val = '[]';
        } else {
            $val = ValueFormatter::format($this->value, $this->type, $this->printIndent());
        }
        $this->output .= ' = ' . $val . ';';

        return $this->output;
    }

    /**
     * Print constant
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
