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
 * Property generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
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
        $type = null;
        if ($this->type !== null) {
            $type = $this->type;
            if (($this->value === null) && !str_starts_with($type, '?') && ($type !== 'mixed')
                && !in_array('null', explode('|', $type), true)) {
                $type .= '|null';
            }
            $type .= ' ';
        }
        $this->output  = PHP_EOL . $this->docblock->render();
        $this->output .= $this->printIndent() . $this->visibility . (($this->static) ? ' static' : '') . ' ' . $type . '$' . $this->name;
        $this->output .= ' = ' . ValueFormatter::format($this->value, $this->type, $this->printIndent()) . ';';

        return $this->output;
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
