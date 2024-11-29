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
 * Class generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.3
 */
class ClassGenerator extends AbstractClassGenerator
{

    use Traits\UseTrait, Traits\PropertiesTrait, Traits\AbstractFinalTrait;

    /**
     * Parent class that is extended
     * @var ?string
     */
    protected ?string $parent = null;

    /**
     * Interfaces that are implemented
     * @var array
     */
    protected array $interfaces = [];

    /**
     * Constructor
     *
     * Instantiate the class generator object
     *
     * @param  string  $name
     * @param  ?string $parent
     * @param  mixed   $interface
     * @param  bool    $abstract
     */
    public function __construct(string $name, ?string $parent = null, mixed $interface = null, bool $abstract = false)
    {
        $this->setName($name);

        if ($parent !== null) {
            $this->setParent($parent);
        }

        if ($interface !== null) {
            if (is_array($interface)) {
                $this->addInterfaces($interface);
            } else if (str_contains($interface, ',')) {
                $this->addInterfaces(array_map('trim', explode(',', $interface)));
            } else {
                $this->addInterface($interface);
            }
        }

        $this->setAsAbstract($abstract);
    }

    /**
     * Set the class parent
     *
     * @param  ?string $parent
     * @return ClassGenerator
     */
    public function setParent(?String $parent = null): ClassGenerator
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the class parent
     *
     * @return string|null
     */
    public function getParent(): string|null
    {
        return $this->parent;
    }

    /**
     * Has parent
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return ($this->parent !== null);
    }

    /**
     * Add a class interface
     *
     * @param  string $interface
     * @return ClassGenerator
     */
    public function addInterface(string $interface): ClassGenerator
    {
        if (!in_array($interface, $this->interfaces)) {
            $this->interfaces[] = $interface;
        }

        return $this;
    }

    /**
     * Add a class interface
     *
     * @param  array $interfaces
     * @return ClassGenerator
     */
    public function addInterfaces(array $interfaces): ClassGenerator
    {
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }

        return $this;
    }

    /**
     * Get the class interfaces
     *
     * @return array
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * Has class interfaces
     *
     * @return bool
     */
    public function hasInterfaces(): bool
    {
        return (!empty($this->interfaces));
    }

    /**
     * Has class interface
     *
     * @param  string $interface
     * @return bool
     */
    public function hasInterface(string $interface): bool
    {
        return (in_array($interface, $this->interfaces));
    }

    /**
     * Remove class interface
     *
     * @param  string $interface
     * @return ClassGenerator
     */
    public function removeInterface(string $interface): ClassGenerator
    {
        if (in_array($interface, $this->interfaces)) {
            $key = array_search($interface, $this->interfaces);
            unset($this->interfaces[$key]);
        }

        return $this;
    }

    /**
     * Render class
     *
     * @return string
     */
    public function render(): string
    {
        $classKeyword = null;

        if ($this->isAbstract()) {
            $classKeyword = 'abstract ';
        } else if ($this->isFinal()) {
            $classKeyword = 'final ';
        }

        $this->output  = ($this->namespace !== null) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= ($this->docblock !== null) ? $this->docblock->render() : null;
        $this->output .= $classKeyword . 'class ' . $this->name;

        if ($this->parent !== null) {
            $this->output .= ' extends ' . $this->parent;
        }
        if (!empty($this->interfaces)) {
            $this->output .= ' implements ' . implode(', ', $this->interfaces);
        }

        $this->output .= PHP_EOL . '{' . PHP_EOL;

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

        if ($this->hasConstants()) {
            $this->output .= $this->formatConstants();
        }
        if ($this->hasProperties()) {
            $this->output .= $this->formatProperties();
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
     * Format the properties
     *
     * @return string
     */
    protected function formatProperties(): string
    {
        $props = null;

        foreach ($this->properties as $prop) {
            $props .= $prop->render() . PHP_EOL;
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
            $methods .= $method->render() . PHP_EOL;
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
