<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator\Traits;

use Pop\Code\Generator\AttributeGenerator;

/**
 * Attributes trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    6.0.0
 */
trait AttributesTrait
{

    /**
     * Attributes. An indexed list, not keyed by name — attributes can legitimately repeat
     * (Attribute::IS_REPEATABLE), so a name-keyed map would silently collapse repeats.
     * @var array
     */
    protected array $attributes = [];

    /**
     * Add an attribute
     *
     * @param  AttributeGenerator $attribute
     * @return static
     */
    public function addAttribute(AttributeGenerator $attribute): static
    {
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * Add attributes
     *
     * @param  array $attributes
     * @return static
     */
    public function addAttributes(array $attributes): static
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
        return $this;
    }

    /**
     * Has attributes
     *
     * @return bool
     */
    public function hasAttributes(): bool
    {
        return !empty($this->attributes);
    }

    /**
     * Has an attribute with the given name
     *
     * @param  string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get all attributes with the given name (supports repeated attributes)
     *
     * @param  string $name
     * @return array
     */
    public function getAttributesByName(string $name): array
    {
        $matches = [];
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                $matches[] = $attribute;
            }
        }
        return $matches;
    }

    /**
     * Remove an attribute (by instance identity — there is no unique name to remove by)
     *
     * @param  AttributeGenerator $attribute
     * @return static
     */
    public function removeAttribute(AttributeGenerator $attribute): static
    {
        $key = array_search($attribute, $this->attributes, true);
        if ($key !== false) {
            unset($this->attributes[$key]);
            $this->attributes = array_values($this->attributes);
        }
        return $this;
    }

    /**
     * Format the attributes
     *
     * @param  bool $indented
     * @return string
     */
    protected function formatAttributes(bool $indented = true): string
    {
        $output = '';
        $prefix = $indented ? $this->printIndent() : '';

        foreach ($this->attributes as $attribute) {
            $output .= $prefix . $attribute->render() . PHP_EOL;
        }

        return $output;
    }

}
