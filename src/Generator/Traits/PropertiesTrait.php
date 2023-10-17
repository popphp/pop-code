<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator\Traits;

use Pop\Code\Generator\PropertyGenerator;

/**
 * Properties trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait PropertiesTrait
{

    /**
     * Array of property generator objects
     * @var array
     */
    protected array $properties = [];

    /**
     * Add class properties
     *
     * @param  array $properties
     * @return static
     */
    public function addProperties(array $properties): static
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }

        return $this;
    }

    /**
     * Add a class property
     *
     * @param  PropertyGenerator $property
     * @return static
     */
    public function addProperty(PropertyGenerator $property): static
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * Get a class property
     *
     * @param  mixed $property
     * @return PropertyGenerator|null
     */
    public function getProperty(mixed $property): PropertyGenerator|null
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        return (isset($this->properties[$p])) ? $this->properties[$p] : null;
    }

    /**
     * Get all properties
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Has a class property
     *
     * @param  mixed $property
     * @return bool
     */
    public function hasProperty(mixed $property): bool
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        return (isset($this->properties[$p]));
    }

    /**
     * Has properties
     *
     * @return bool
     */
    public function hasProperties(): bool
    {
        return (!empty($this->properties));
    }

    /**
     * Remove a class property
     *
     * @param  mixed $property
     * @return static
     */
    public function removeProperty(mixed $property): static
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        if (isset($this->properties[$p])) {
            unset($this->properties[$p]);
        }
        return $this;
    }

}