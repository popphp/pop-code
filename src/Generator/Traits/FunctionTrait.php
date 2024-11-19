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
namespace Pop\Code\Generator\Traits;

use Pop\Code\Generator\DocblockGenerator;
use InvalidArgumentException;

/**
 * Function trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait FunctionTrait
{

    /**
     * Arguments
     * @var array
     */
    protected array $arguments = [];

    /**
     * Return Types
     * @var array
     */
    protected array $returnTypes = [];

    /**
     * Add an argument
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  ?string $type
     * @return static
     */
    public function addArgument(string $name, mixed $value = null, ?string $type = null): static
    {
        $typeHintsNotAllowed = ['integer'];
        $argType = (!in_array($type, $typeHintsNotAllowed)) ? $type : null;
        $this->arguments[$name] = ['value' => $value, 'type' => $argType];

        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        if (!str_starts_with($name, '$')) {
            $name = '$' . $name;
        }
        if ($value == 'null') {
            $type .= '|null';
        }
        $this->docblock->addParam($type, $name);

        return $this;
    }

    /**
     * Add arguments
     *
     * @param  array $args
     * @throws InvalidArgumentException
     * @return static
     */
    public function addArguments(array $args): static
    {
        foreach ($args as $arg) {
            if (!isset($arg['name'])) {
                throw new InvalidArgumentException("Error: The 'name' key was not set.");
            }
            $value = (isset($arg['value'])) ? $arg['value'] : null;
            $type  = (isset($arg['type'])) ? $arg['type'] : null;
            $this->addArgument($arg['name'], $value, $type);
        }
        return $this;
    }

    /**
     * Has an argument
     *
     * @param  string $name
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    /**
     * Has arguments
     *
     * @return bool
     */
    public function hasArguments(): bool
    {
        return !empty($this->arguments);
    }

    /**
     * Get an argument
     *
     * @param  string $name
     * @return array|null
     */
    public function getArgument(string $name): array|null
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * Get the arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Add an argument (alias method for convenience)
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  ?string $type
     * @return static
     */
    public function addParameter(string $name, mixed $value = null, ?string $type = null): static
    {
        $this->addArgument($name, $value, $type);
        return $this;
    }

    /**
     * Add arguments (alias method for convenience)
     *
     * @param  array $args
     * @return static
     */
    public function addParameters(array $args): static
    {
        $this->addArguments($args);
        return $this;
    }

    /**
     * Has an argument (alias method for convenience)
     *
     * @param  string $name
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return $this->hasArgument($name);
    }

    /**
     * Has arguments (alias method for convenience)
     *
     * @return bool
     */
    public function hasParameters(): bool
    {
        return $this->hasArguments();
    }

    /**
     * Get an argument (alias method for convenience)
     *
     * @param  string $name
     * @return array|null
     */
    public function getParameter(string $name): array|null
    {
        return $this->getArgument($name);
    }

    /**
     * Get the arguments (alias method for convenience)
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->getArguments();
    }

    /**
     * Add a return type
     *
     * @param  string $type
     * @return static
     */
    public function addReturnType(string $type): static
    {
        $typeHintsNotAllowed = ['integer'];
        if (!in_array($type, $typeHintsNotAllowed)) {
            $this->returnTypes[] = $type;

            if ($this->docblock === null) {
                $this->docblock = new DocblockGenerator(null, $this->indent);
            }

            $this->docblock->setReturn(implode('|', $this->returnTypes));
        }

        return $this;
    }

    /**
     * Add return types
     *
     * @param  array $types
     * @return static
     */
    public function addReturnTypes(array $types): static
    {
        foreach ($types as $type) {
            $this->addReturnType($type);
        }
        return $this;
    }

    /**
     * Has return type
     *
     * @param  string $type
     * @return bool
     */
    public function hasReturnType(string $type): bool
    {
        return in_array($type, $this->returnTypes);
    }

    /**
     * Has return types
     *
     * @return bool
     */
    public function hasReturnTypes(): bool
    {
        return !empty($this->returnTypes);
    }

    /**
     * Get the return types
     *
     * @return array
     */
    public function getReturnTypes(): array
    {
        return $this->returnTypes;
    }

    /**
     * Format the arguments
     *
     * @return string|null
     */
    protected function formatArguments(): string|null
    {
        $args = null;

        $i = 0;
        foreach ($this->arguments as $name => $arg) {
            $i++;
            if ($arg['type'] !== null) {
                $type = $arg['type'];
                if ($arg['value'] == 'null') {
                    $type .= '|null';
                }
                $args .= $type . ' ';
            }
            $args .= (substr($name, 0, 1) != '$') ? "\$" . $name : $name;
            $args .= ($arg['value'] !== null) ? " = " . $arg['value'] : null;
            if ($i < count($this->arguments)) {
                $args .= ', ';
            }
        }

        return $args;
    }

}
