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
 * Namespace generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class NamespaceGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait;

    /**
     * Array of namespaces to use
     * @var array
     */
    protected $use = [];

    /**
     * Constructor
     *
     * Instantiate the namespace generator object
     *
     * @param  string $namespace
     */
    public function __construct($namespace)
    {
        $this->setName($namespace);
    }

    /**
     * Set a namespace to use
     *
     * @param  string $use
     * @param  string $as
     * @return NamespaceGenerator
     */
    public function setUse($use, $as = null)
    {
        $this->use[$use] = $as;
        return $this;
    }

    /**
     * Set namespaces to use
     *
     * @param  array $uses
     * @return NamespaceGenerator
     */
    public function setUses(array $uses)
    {
        foreach ($uses as $use) {
            if (is_array($use)) {
                $this->use[$use[0]] = (isset($use[1])) ? $use[1] : null;
            } else {
                $this->use[$use] = null;
            }
        }
        return $this;
    }

    /**
     * Get use
     *
     * @return array
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * Render namespace
     *
     * @return string
     */
    public function render()
    {
        $this->docblock = new DocblockGenerator(null, $this->indent);
        $this->docblock->setTag('namespace');

        $this->output  = $this->docblock->render();
        $this->output .= $this->printIndent() . 'namespace ' . $this->name . ';' . PHP_EOL;

        if (count($this->use) > 0) {
            $this->output .= PHP_EOL;
            foreach ($this->use as $ns => $as) {
                $this->output .= $this->printIndent() . 'use ';
                $this->output .= $ns;
                if (null !== $as) {
                    $this->output .= ' as ' . $as;
                }
                $this->output .= ';' . PHP_EOL;
            }
        }

        return $this->output;
    }

    /**
     * Print namespace
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}