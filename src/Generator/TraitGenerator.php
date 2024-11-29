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
 * Trait generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
class TraitGenerator extends AbstractClassGenerator
{

    use Traits\UseTrait, Traits\PropertiesTrait;

    /**
     * Constructor
     *
     * Instantiate the trait generator object
     *
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }
    /**
     * Render class
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = ($this->namespace !== null) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= ($this->docblock !== null) ? $this->docblock->render() : null;
        $this->output .= 'trait ' . $this->name;

        $this->output .= PHP_EOL . '{';

        if ($this->hasUses()) {
            $this->output .= PHP_EOL;
            foreach ($this->uses as $ns => $as) {
                $this->output .= $this->printIndent() . 'use ';
                $this->output .= $ns;
                if ($as !== null) {
                    $this->output .= ' as ' . $as;
                }
                $this->output .= ';' . PHP_EOL;
            }
        }

        $this->output .= $this->formatConstants() . PHP_EOL;
        $this->output .= $this->formatProperties() . PHP_EOL;
        $this->output .= $this->formatMethods() . PHP_EOL;
        $this->output .= '}' . PHP_EOL;

        return $this->output;
    }

    /**
     * Format the constants
     *
     * @return string
     */
    protected function formatConstants(): string
    {
        $constants = null;

        foreach ($this->constants as $constant) {
            $constants .= PHP_EOL . $constant->render();
        }

        return $constants;
    }

    /**
     * Format the properties
     *
     * @return string
     */
    protected function formatProperties(): string
    {
        $props = null;

        foreach ($this->properties as $prop) {
            $props .= PHP_EOL . $prop->render();
        }

        return $props;
    }

    /**
     * Format the methods
     *
     * @return string
     */
    protected function formatMethods(): string
    {
        $methods = null;

        foreach ($this->methods as $method) {
            $methods .= PHP_EOL . $method->render();
        }

        return $methods;
    }

    /**
     * Print class
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
