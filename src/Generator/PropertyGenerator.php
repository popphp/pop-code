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
     * Readonly flag
     * @var bool
     */
    protected bool $readonly = false;

    /**
     * Flag to suppress printing the redundant 'readonly' keyword when the enclosing
     * class is itself declared readonly (the property remains readonly regardless)
     * @var bool
     */
    protected bool $suppressReadonlyKeyword = false;

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
     * Set the readonly flag
     *
     * @param  bool $readonly
     * @return PropertyGenerator
     */
    public function setAsReadonly(bool $readonly = true): PropertyGenerator
    {
        $this->readonly = $readonly;
        if ($this->readonly) {
            $this->setAsStatic(false);
        }
        return $this;
    }

    /**
     * Get the readonly flag
     *
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Suppress printing the 'readonly' keyword on this property, e.g. because the enclosing
     * class is itself declared readonly, making a per-property 'readonly' keyword redundant.
     * The property is still treated as readonly for type/value rendering purposes.
     *
     * @param  bool $suppress
     * @return PropertyGenerator
     */
    public function suppressReadonlyKeyword(bool $suppress = true): PropertyGenerator
    {
        $this->suppressReadonlyKeyword = $suppress;
        return $this;
    }

    /**
     * Set the static flag (overridden to enforce mutual exclusion with readonly)
     *
     * @param  bool $static
     * @return PropertyGenerator
     */
    public function setAsStatic(bool $static = true): PropertyGenerator
    {
        parent::setAsStatic($static);
        if ($static) {
            $this->readonly = false;
        }
        return $this;
    }

    /**
     * Render property
     *
     * @throws Exception
     * @return string
     */
    public function render(): string
    {
        if ($this->readonly && ($this->type === null)) {
            throw new Exception('Error: A readonly property must have a type.');
        }

        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        $this->docblock->addTag('var', $this->type);
        $type = null;
        if ($this->type !== null) {
            $type = $this->type;
            if (!$this->readonly && ($this->value === null) && !str_starts_with($type, '?') && ($type !== 'mixed')
                && !in_array('null', explode('|', $type), true)) {
                $type .= '|null';
            }
            $type .= ' ';
        }
        $this->output  = PHP_EOL . $this->docblock->render();
        $this->output .= $this->printIndent() . $this->visibility . (($this->static) ? ' static' : '')
            . (($this->readonly && !$this->suppressReadonlyKeyword) ? ' readonly' : '') . ' ' . $type . '$' . $this->name;

        if ($this->readonly) {
            $this->output .= ';';
        } else {
            $this->output .= ' = ' . ValueFormatter::format($this->value, $this->type, $this->printIndent()) . ';';
        }

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
