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
namespace Pop\Code\Generator;

/**
 * Property generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class PropertyGenerator extends AbstractClassElementGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait;

    /**
     * Property type
     * @var ?string
     */
    protected ?string $type = null;

    /**
     * Property value
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * Constructor
     *
     * Instantiate the property generator object
     *
     * @param  string $name
     * @param  ?string $type
     * @param  mixed $value
     * @param  string $visibility
     * @param  bool $static
     * @throws Exception
     */
    public function __construct(
        string $name, ?string $type = null, mixed $value = null, string $visibility = 'public', bool $static = false
    )
    {
        $this->setName($name);
        if ($type !== null) {
            $this->setType($type);
        }
        if ($value !== null) {
            $this->setValue($value);
        }
        $this->setVisibility($visibility);
        $this->setAsStatic($static);
    }

    /**
     * Set the property type
     *
     * @param  string $type
     * @return PropertyGenerator
     */
    public function setType(string $type): PropertyGenerator
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the property type
     *
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Has property type
     *
     * @return bool
     */
    public function hasType(): bool
    {
        return ($this->type !== null);
    }

    /**
     * Set the property value
     *
     * @param  mixed $value
     * @return PropertyGenerator
     */
    public function setValue(mixed $value = null): PropertyGenerator
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the property value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Has property value
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    /**
     * Render property
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
        $this->output .= $this->printIndent() . $this->visibility . (($this->static) ? ' static' : '') . ' $' . $this->name;

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
     * Print property
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}