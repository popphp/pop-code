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
 * Property generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class PropertyGenerator extends AbstractClassElementGenerator
{

    use NameTrait, DocblockTrait;

    /**
     * Property type
     * @var string
     */
    protected $type = null;

    /**
     * Property value
     * @var mixed
     */
    protected $value = null;

    /**
     * Constructor
     *
     * Instantiate the property generator object
     *
     * @param  string  $name
     * @param  string  $type
     * @param  mixed   $value
     * @param  string  $visibility
     * @param  boolean $static
     */
    public function __construct($name, $type, $value = null, $visibility = 'public', $static = false)
    {
        $this->setName($name);
        $this->setType($type);
        $this->setValue($value);
        $this->setVisibility($visibility);
        $this->setAsStatic($static);
    }


    /**
     * Set the property type
     *
     * @param  string $type
     * @return PropertyGenerator
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the property type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the property value
     *
     * @param  mixed $value
     * @return PropertyGenerator
     */
    public function setValue($value = null)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the property value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Has property value
     *
     * @return boolean
     */
    public function hasValue()
    {
        return (null !== $this->value);
    }

    /**
     * Render property
     *
     * @return mixed
     */
    public function render()
    {
        $static = null;
        if ($this->visibility != 'const') {
            $varDeclaration = ' $';
            if ($this->static) {
                $static = ' static';
            }
        } else {
            $varDeclaration = ' ';
        }

        if (null === $this->docblock) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }
        $this->docblock->setTag('var', $this->type);
        $this->output = PHP_EOL . $this->docblock->render();
        $this->output .= $this->printIndent() . $this->visibility . $static . $varDeclaration . $this->name;

        if (null !== $this->value) {
            if ($this->type == 'array') {
                $val = (count($this->value) == 0) ? '[]' : $this->formatArrayValues();
                $this->output .= ' = ' . $val . PHP_EOL;
            } else if (($this->type == 'integer') || ($this->type == 'int') || ($this->type == 'float')) {
                $this->output .= ' = ' . $this->value . ';';
            } else if ($this->type == 'boolean') {
                $val = ($this->value) ? 'true' : 'false';
                $this->output .= " = " . $val . ";";
            } else {
                $this->output .= " = '" . $this->value . "';";
            }
        } else {
            $val = ($this->type == 'array') ? '[]' : 'null';
            $this->output .= ' = ' . $val . ';';
        }

        return $this->output;
    }

    /**
     * Format array value
     *
     * @return string
     */
    protected function formatArrayValues()
    {
        $ary = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '  ', var_export($this->value, true));
        $ary .= ';';
        $ary = str_replace('array (', '[', $ary);
        $ary = str_replace('  );', '];', $ary);
        $ary = str_replace('NULL', 'null', $ary);

        $keys = array_keys($this->value);

        $isAssoc = false;

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $i) {
                $isAssoc = true;
            }
        }

        if (!$isAssoc) {
            for ($i = 0; $i < count($keys); $i++) {
                $ary = str_replace($i . ' => ', '', $ary);
            }
        }

        return $ary;
    }

    /**
     * Print property
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}