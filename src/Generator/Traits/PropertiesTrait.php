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
namespace Pop\Code\Generator\Traits;

use Pop\Code\Generator\PropertyGenerator;

/**
 * Properties trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
trait PropertiesTrait
{

    /**
     * Array of property generator objects
     * @var array
     */
    protected $properties = [];

    /**
     * Add class properties
     *
     * @param  array $properties
     * @return PropertiesTrait
     */
    public function addProperties(array $properties)
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
     * @return PropertiesTrait
     */
    public function addProperty(PropertyGenerator $property)
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * Get a class property
     *
     * @param  mixed $property
     * @return PropertyGenerator
     */
    public function getProperty($property)
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        return (isset($this->properties[$p])) ? $this->properties[$p] : null;
    }

    /**
     * Get all properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Has a class property
     *
     * @param  mixed $property
     * @return boolean
     */
    public function hasProperty($property)
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        return (isset($this->properties[$p]));
    }

    /**
     * Has properties
     *
     * @return boolean
     */
    public function hasProperties()
    {
        return (!empty($this->properties));
    }

    /**
     * Remove a class property
     *
     * @param  mixed $property
     * @return PropertiesTrait
     */
    public function removeProperty($property)
    {
        $p = ($property instanceof PropertyGenerator) ? $property->getName() : $property;
        if (isset($this->properties[$p])) {
            unset($this->properties[$p]);
        }
        return $this;
    }

}