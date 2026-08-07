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

/**
 * Interface generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class InterfaceGenerator extends AbstractClassGenerator
{

    /**
     * Parent interfaces that are extended -- a PHP interface can extend more than one interface
     * at once (`interface Foo extends A, B`), unlike a class, which can only extend one
     * @var array
     */
    protected array $parents = [];

    /**
     * Constructor
     *
     * Instantiate the interface generator object
     *
     * @param  string $name
     * @param  mixed  $parent
     */
    public function __construct(string $name, mixed $parent = null)
    {
        $this->setName($name);

        if ($parent !== null) {
            if (is_array($parent)) {
                $this->addParents($parent);
            } else if (str_contains($parent, ',')) {
                $this->addParents(array_map('trim', explode(',', $parent)));
            } else {
                $this->addParent($parent);
            }
        }
    }

    /**
     * Add a parent interface
     *
     * @param  string $parent
     * @return InterfaceGenerator
     */
    public function addParent(string $parent): InterfaceGenerator
    {
        if (!in_array($parent, $this->parents)) {
            $this->parents[] = $parent;
        }

        return $this;
    }

    /**
     * Add parent interfaces
     *
     * @param  array $parents
     * @return InterfaceGenerator
     */
    public function addParents(array $parents): InterfaceGenerator
    {
        foreach ($parents as $parent) {
            $this->addParent($parent);
        }

        return $this;
    }

    /**
     * Get the parent interfaces
     *
     * @return array
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * Has parent interfaces
     *
     * @return bool
     */
    public function hasParents(): bool
    {
        return (!empty($this->parents));
    }

    /**
     * Has a specific parent interface
     *
     * @param  string $parent
     * @return bool
     */
    public function hasParent(string $parent): bool
    {
        return (in_array($parent, $this->parents));
    }

    /**
     * Remove a parent interface
     *
     * @param  string $parent
     * @return InterfaceGenerator
     */
    public function removeParent(string $parent): InterfaceGenerator
    {
        if (in_array($parent, $this->parents)) {
            $key = array_search($parent, $this->parents);
            unset($this->parents[$key]);
        }

        return $this;
    }

    /**
     * Render interface
     *
     * @return string
     */
    public function render(): string
    {
        $this->output  = ($this->namespace !== null) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= ($this->docblock !== null) ? $this->docblock->render() : null;
        $this->output .= $this->formatAttributes(false);
        $this->output .= 'interface ' . $this->name;

        if ($this->hasParents()) {
            $this->output .= ' extends ' . implode(', ', $this->parents);
        }

        $this->output .= PHP_EOL . '{' . PHP_EOL;

        if ($this->hasConstants()) {
            $this->output .= $this->formatConstants();
        }
        if ($this->hasMethods()) {
            $this->output .= $this->formatMethods();
        }

        $this->output .= PHP_EOL . '}' . PHP_EOL;

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
            $constants .= $constant->render() . PHP_EOL;
        }

        return $constants;
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
            $methods .= $method->render() . PHP_EOL;
        }

        return $methods;
    }

    /**
     * Print interface
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
