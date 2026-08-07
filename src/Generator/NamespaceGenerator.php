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
 * Namespace generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class NamespaceGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\UseTrait;

    /**
     * Constructor
     *
     * Instantiate the namespace generator object
     *
     * @param  ?string $namespace
     * @param  int     $indent
     */
    public function __construct(?string $namespace = null, int $indent = 0)
    {
        if ($namespace !== null) {
            $this->setName($namespace);
        }
        $this->setIndent($indent);
    }

    /**
     * Render namespace
     *
     * @return string
     */
    public function render(): string
    {
        // Only create a fresh docblock if one hasn't already been set -- render() used to
        // unconditionally overwrite $this->docblock, silently discarding any docblock a caller
        // had set via DocblockTrait's setDocblock()/setDesc(), despite this class mixing that
        // trait in.
        if ($this->docblock === null) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }
        if (!$this->docblock->hasTag('namespace')) {
            $this->docblock->addTag('namespace');
        }

        $this->output  = $this->docblock->render();

        if (!empty($this->name)) {
            $this->output .= $this->printIndent() . 'namespace ' . $this->name . ';' . PHP_EOL;
        }

        if ($this->hasUses()) {
            $this->output .= PHP_EOL;
            foreach ($this->uses as $ns => $as) {
                $this->output .= $this->printIndent() . 'use ';
                $this->output .= $ns;
                if ($as !== null) {
                    $this->output .= ' as ' . $as;
                }
                $this->output .= ';' . PHP_EOL . PHP_EOL;
            }
        }

        return $this->output;
    }

    /**
     * Print namespace
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
