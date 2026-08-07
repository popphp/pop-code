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
 * Enum case generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class EnumCaseGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\AttributesTrait;

    /**
     * Case value (backed enums only)
     * @var int|string|null
     */
    protected int|string|null $value = null;

    /**
     * Constructor
     *
     * Instantiate the enum case generator object
     *
     * @param  string          $name
     * @param  int|string|null $value
     */
    public function __construct(string $name, int|string|null $value = null)
    {
        $this->setName($name);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Set the case value
     *
     * @param  int|string|null $value
     * @return EnumCaseGenerator
     */
    public function setValue(int|string|null $value = null): EnumCaseGenerator
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the case value
     *
     * @return int|string|null
     */
    public function getValue(): int|string|null
    {
        return $this->value;
    }

    /**
     * Has case value
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    /**
     * Render case
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = PHP_EOL . (($this->docblock !== null) ? $this->docblock->render() : null);
        $this->output .= $this->hasAttributes() ? $this->formatAttributes() : null;
        $this->output .= $this->printIndent() . 'case ' . $this->name;

        if ($this->value !== null) {
            $type = is_int($this->value) ? 'int' : 'string';
            $this->output .= ' = ' . ValueFormatter::format($this->value, $type) . ';';
        } else {
            $this->output .= ';';
        }

        return $this->output;
    }

    /**
     * Print case
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
