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
 * Enum generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class EnumGenerator extends AbstractClassGenerator
{

    use Traits\UseTrait;

    /**
     * Backing type (null for a pure enum, otherwise 'int' or 'string')
     * @var ?string
     */
    protected ?string $backingType = null;

    /**
     * Interfaces that are implemented
     * @var array
     */
    protected array $interfaces = [];

    /**
     * Array of enum case generator objects
     * @var array
     */
    protected array $cases = [];

    /**
     * Constructor
     *
     * Instantiate the enum generator object
     *
     * @param  string  $name
     * @param  ?string $backingType
     * @param  mixed   $interface
     */
    public function __construct(string $name, ?string $backingType = null, mixed $interface = null)
    {
        $this->setName($name);

        if ($backingType !== null) {
            $this->setBackingType($backingType);
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
    }

    /**
     * Set the backing type
     *
     * @param  ?string $backingType
     * @return EnumGenerator
     */
    public function setBackingType(?string $backingType = null): EnumGenerator
    {
        $this->backingType = $backingType;
        return $this;
    }

    /**
     * Get the backing type
     *
     * @return string|null
     */
    public function getBackingType(): string|null
    {
        return $this->backingType;
    }

    /**
     * Has backing type
     *
     * @return bool
     */
    public function hasBackingType(): bool
    {
        return ($this->backingType !== null);
    }

    /**
     * Add an interface
     *
     * @param  string $interface
     * @return EnumGenerator
     */
    public function addInterface(string $interface): EnumGenerator
    {
        if (!in_array($interface, $this->interfaces)) {
            $this->interfaces[] = $interface;
        }

        return $this;
    }

    /**
     * Add interfaces
     *
     * @param  array $interfaces
     * @return EnumGenerator
     */
    public function addInterfaces(array $interfaces): EnumGenerator
    {
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }

        return $this;
    }

    /**
     * Get the interfaces
     *
     * @return array
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * Has interfaces
     *
     * @return bool
     */
    public function hasInterfaces(): bool
    {
        return (!empty($this->interfaces));
    }

    /**
     * Has interface
     *
     * @param  string $interface
     * @return bool
     */
    public function hasInterface(string $interface): bool
    {
        return (in_array($interface, $this->interfaces));
    }

    /**
     * Remove interface
     *
     * @param  string $interface
     * @return EnumGenerator
     */
    public function removeInterface(string $interface): EnumGenerator
    {
        if (in_array($interface, $this->interfaces)) {
            $key = array_search($interface, $this->interfaces);
            unset($this->interfaces[$key]);
        }

        return $this;
    }

    /**
     * Add cases
     *
     * @param  array $cases
     * @return EnumGenerator
     */
    public function addCases(array $cases): EnumGenerator
    {
        foreach ($cases as $case) {
            $this->addCase($case);
        }
        return $this;
    }

    /**
     * Add a case
     *
     * @param  EnumCaseGenerator $case
     * @return EnumGenerator
     */
    public function addCase(EnumCaseGenerator $case): EnumGenerator
    {
        $this->cases[$case->getName()] = $case;
        return $this;
    }

    /**
     * Get a case
     *
     * @param  mixed $case
     * @return EnumCaseGenerator|null
     */
    public function getCase(mixed $case): EnumCaseGenerator|null
    {
        $c = ($case instanceof EnumCaseGenerator) ? $case->getName() : $case;
        return (isset($this->cases[$c])) ? $this->cases[$c] : null;
    }

    /**
     * Has a case
     *
     * @param  mixed $case
     * @return bool
     */
    public function hasCase(mixed $case): bool
    {
        $c = ($case instanceof EnumCaseGenerator) ? $case->getName() : $case;
        return (isset($this->cases[$c]));
    }

    /**
     * Has cases
     *
     * @return bool
     */
    public function hasCases(): bool
    {
        return (!empty($this->cases));
    }

    /**
     * Get all cases
     *
     * @return array
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * Remove a case
     *
     * @param  mixed $case
     * @return EnumGenerator
     */
    public function removeCase(mixed $case): EnumGenerator
    {
        $c = ($case instanceof EnumCaseGenerator) ? $case->getName() : $case;
        if (isset($this->cases[$c])) {
            unset($this->cases[$c]);
        }
        return $this;
    }

    /**
     * Add a method (overridden to reject a constructor, which enums cannot declare)
     *
     * @param  MethodGenerator $method
     * @throws Exception
     * @return EnumGenerator
     */
    public function addMethod(MethodGenerator $method): EnumGenerator
    {
        if (strtolower($method->getName()) === '__construct') {
            throw new Exception('Error: Enums cannot declare a constructor.');
        }
        parent::addMethod($method);
        return $this;
    }

    /**
     * Render enum
     *
     * @throws Exception
     * @return string
     */
    public function render(): string
    {
        foreach ($this->cases as $case) {
            if ($this->hasBackingType() && !$case->hasValue()) {
                throw new Exception("Error: Case '" . $case->getName() . "' of a backed enum must have a value.");
            }
            if (!$this->hasBackingType() && $case->hasValue()) {
                throw new Exception("Error: Case '" . $case->getName() . "' of a non-backed enum must not have a value.");
            }
        }

        $this->output  = ($this->namespace !== null) ? $this->namespace->render() . PHP_EOL : null;
        $this->output .= ($this->docblock !== null) ? $this->docblock->render() : null;
        $this->output .= $this->formatAttributes(false);
        $this->output .= 'enum ' . $this->name;

        if ($this->backingType !== null) {
            $this->output .= ': ' . $this->backingType;
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

        if ($this->hasCases()) {
            $this->output .= $this->formatCases();
        }
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
     * Format the cases
     *
     * @return string
     */
    protected function formatCases(): string
    {
        $cases = null;

        foreach ($this->cases as $case) {
            $cases .= $case->render() . PHP_EOL;
        }

        return $cases;
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
     * Print enum
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
