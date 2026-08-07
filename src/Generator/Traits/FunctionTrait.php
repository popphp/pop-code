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
namespace Pop\Code\Generator\Traits;

use Pop\Code\Generator\DocblockGenerator;
use Pop\Code\Generator\Exception;
use Pop\Code\Generator\NoValue;
use Pop\Code\Generator\Support\ValueFormatter;

/**
 * Function trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
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
     * @param  bool    $variadic
     * @param  bool    $byRef
     * @param  array   $attributes
     * @throws Exception
     * @return static
     */
    public function addArgument(
        string $name, mixed $value = new NoValue(), ?string $type = null, bool $variadic = false, bool $byRef = false,
        array $attributes = []
    ): static
    {
        if ($variadic && !($value instanceof NoValue)) {
            throw new Exception('Error: A variadic argument cannot have a default value.');
        }

        $this->arguments[$name] = [
            'value' => $value, 'type' => $type, 'variadic' => $variadic, 'byRef' => $byRef, 'attributes' => $attributes
        ];

        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        $docName = $name;
        if (!str_starts_with($docName, '$')) {
            $docName = '$' . $docName;
        }
        $docType = $type;
        if (!empty($docType) && !str_starts_with($docType, '?') && ($docType !== 'mixed') && ($value === null)
            && !in_array('null', explode('|', $docType), true)
        ) {
            $docType = str_contains($docType, '&') ? '(' . $docType . ')|null' : $docType . '|null';
        }
        // A caller re-adding an argument for a name that already exists (e.g. to change its
        // type) leaves a stale @param entry behind otherwise -- $this->arguments is name-keyed
        // and correctly overwrites, but the docblock's params are append-only. This also covers
        // MethodReflection/FunctionReflection's own flow: they set a fully-parsed docblock
        // (which may carry a hand-written per-param description from the real source) *before*
        // calling addArgument() for each parameter -- preserve that description across the
        // remove+re-add rather than silently dropping it, since addArgument() itself has no way
        // to be told a description directly.
        $existingParam = $this->docblock->findParam($docName);
        $docDesc       = $existingParam['desc'] ?? null;
        $this->docblock->removeParam($docName);
        $this->docblock->addParam($docType, $docName, $docDesc);

        return $this;
    }

    /**
     * Add arguments
     *
     * @param  array $args  each element shaped ['name' => string, 'value' => mixed, 'type' => ?string,
     *                       'variadic' => bool, 'byRef' => bool, 'attributes' => array]
     * @throws Exception
     * @return static
     */
    public function addArguments(array $args): static
    {
        foreach ($args as $arg) {
            if (!isset($arg['name'])) {
                throw new Exception("Error: The 'name' key was not set.");
            }
            $value      = array_key_exists('value', $arg) ? $arg['value'] : new NoValue();
            $type       = $arg['type'] ?? null;
            $variadic   = $arg['variadic'] ?? false;
            $byRef      = $arg['byRef'] ?? false;
            $attributes = $arg['attributes'] ?? [];
            $this->addArgument($arg['name'], $value, $type, $variadic, $byRef, $attributes);
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
     * @param  bool    $variadic
     * @param  bool    $byRef
     * @param  array   $attributes
     * @throws Exception
     * @return static
     */
    public function addParameter(
        string $name, mixed $value = new NoValue(), ?string $type = null, bool $variadic = false, bool $byRef = false,
        array $attributes = []
    ): static
    {
        $this->addArgument($name, $value, $type, $variadic, $byRef, $attributes);
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
        $this->returnTypes[] = $type;

        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        // Preserve an existing @return description (e.g. one already parsed from a real source
        // docblock by MethodReflection/FunctionReflection before this is called) -- setReturn()
        // always resets the description to null when not given one explicitly, which silently
        // discarded it otherwise.
        $existingReturn = $this->docblock->getReturn();
        $returnDesc     = $existingReturn['desc'] ?? null;
        $this->docblock->setReturn(implode('|', $this->returnTypes), $returnDesc);

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

            if (!empty($arg['attributes'])) {
                $attrs = [];
                foreach ($arg['attributes'] as $attribute) {
                    $attrs[] = $attribute->render();
                }
                $args .= implode(' ', $attrs) . ' ';
            }

            $promoted = null;
            if (!empty($arg['promotedVisibility'])) {
                $promoted = $arg['promotedVisibility'] . ' ' . (!empty($arg['promotedReadonly']) ? 'readonly ' : '');
            }

            if ($arg['type'] !== null) {
                $type = $arg['type'];
                if (!empty($type) && !str_starts_with($type, '?') && ($type !== 'mixed') && ($arg['value'] === null)
                    && !in_array('null', explode('|', $type), true)
                ) {
                    // An intersection type (`Countable&Traversable`) needs parens before
                    // combining with `|null` -- PHP requires DNF syntax `(A&B)|null`.
                    $type = str_contains($type, '&') ? '(' . $type . ')|null' : $type . '|null';
                }
                $args .= $promoted . $type . ' ';
            } else {
                $args .= $promoted;
            }

            $args .= (!empty($arg['byRef']) ? '&' : '') . (!empty($arg['variadic']) ? '...' : '');
            $args .= (substr($name, 0, 1) != '$') ? "\$" . $name : $name;

            if (!($arg['value'] instanceof NoValue)) {
                $args .= ' = ' . ValueFormatter::format($arg['value'], $arg['type']);
            }

            if ($i < count($this->arguments)) {
                $args .= ', ';
            }
        }

        return $args;
    }

}
