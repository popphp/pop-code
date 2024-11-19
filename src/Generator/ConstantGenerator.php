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
 * Constant generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class ConstantGenerator extends AbstractClassElementGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait;

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
        $this->output = PHP_EOL . $this->docblock->render();
        $this->output .= $this->printIndent() . 'const ' . $this->name;

        if ($this->value !== null) {
            if ($this->type == 'array') {
                $val = (count($this->value) == 0) ? '[]' : $this->formatArrayValues();
                $this->output .= ' = ' . $val . PHP_EOL;
            } else if (($this->type == 'integer') || ($this->type == 'int') || ($this->type == 'float')) {
                $this->output .= ' = ' . $this->value . ';';
            } else if ($this->type == 'bool') {
                $val = ($this->value) ? 'true' : 'false';
                $this->output .= " = " . $val . ";";
            } else {
                $this->output .= " = '" . $this->value . "';";
            }
        } else {
            $val = ($this->type == 'array') ? '[]' : 'null';
            $this->output .= ' = ' . $val . ';';
        }

        return $this->output;
    }

    /**
     * Format array value
     *
     * @return string
     */
    protected function formatArrayValues(): string
    {
        $ary = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '  ', var_export($this->value, true));
        $ary .= ';';
        $ary = str_replace('array (', '[', $ary);
        $ary = str_replace('  );', '];', $ary);
        $ary = str_replace('NULL', 'null', $ary);

        $keys = array_keys($this->value);

        $isAssoc = false;

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $i) {
                $isAssoc = true;
            }
        }

        if (!$isAssoc) {
            for ($i = 0; $i < count($keys); $i++) {
                $ary = str_replace($i . ' => ', '', $ary);
            }
        }

        return $ary;
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
