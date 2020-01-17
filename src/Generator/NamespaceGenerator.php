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

    use Traits\NameTrait, Traits\DocblockTrait, Traits\UseTrait;

    /**
     * Constructor
     *
     * Instantiate the namespace generator object
     *
     * @param  string $namespace
     * @param  int    $indent
     */
    public function __construct($namespace = null, $indent = 0)
    {
        if (null !== $namespace) {
            $this->setName($namespace);
        }
        $this->setIndent($indent);
    }

    /**
     * Render namespace
     *
     * @return string
     */
    public function render()
    {
        $this->docblock = new DocblockGenerator(null, $this->indent);
        $this->docblock->addTag('namespace');

        $this->output  = $this->docblock->render();

        if (!empty($this->name)) {
            $this->output .= $this->printIndent() . 'namespace ' . $this->name . ';' . PHP_EOL;
        }

        if ($this->hasUses()) {
            $this->output .= PHP_EOL;
            foreach ($this->uses as $ns => $as) {
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