<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class TraitGenerator extends AbstractClassGenerator
{

    use PropertiesTrait;

    /**
     * Constructor
     *
     * Instantiate the trait generator object
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }
    /**
     * Render class
     *
     * @return string
     */
    public function render()
    {
        $this->output = (null !== $this->namespace) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= (null !== $this->docblock) ? $this->docblock->render() : null;
        $this->output .= 'trait ' . $this->name;

        $this->output .= PHP_EOL . '{';
        $this->output .= $this->formatProperties() . PHP_EOL;
        $this->output .= $this->formatMethods() . PHP_EOL;
        $this->output .= '}' . PHP_EOL;

        return $this->output;
    }

    /**
     * Format the properties
     *
     * @return string
     */
    protected function formatProperties()
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
    protected function formatMethods()
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
    public function __toString()
    {
        return $this->render();
    }

}