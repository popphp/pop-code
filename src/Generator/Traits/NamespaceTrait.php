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

use Pop\Code\Generator\NamespaceGenerator;

/**
 * Namespace trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait NamespaceTrait
{

    /**
     * Namespace generator object
     * @var ?NamespaceGenerator
     */
    protected ?NamespaceGenerator $namespace = null;


    /**
     * Set the namespace generator object
     *
     * @param  NamespaceGenerator $namespace
     * @return static
     */
    public function setNamespace(NamespaceGenerator $namespace): static
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Access the namespace generator object
     *
     * @return NamespaceGenerator|null
     */
    public function getNamespace(): NamespaceGenerator|null
    {
        return $this->namespace;
    }

    /**
     * Has a namespace generator object
     *
     * @return bool
     */
    public function hasNamespace(): bool
    {
        return ($this->namespace !== null);
    }

}