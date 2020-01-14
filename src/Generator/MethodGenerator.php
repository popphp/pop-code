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
class MethodGenerator extends AbstractClassElementGenerator
{

    use NameTrait, DocblockTrait;

    /**
     * Method arguments
     * @var array
     */
    protected $arguments = [];

    /**
     * Method abstract flag
     * @var boolean
     */
    protected $abstract = false;

    /**
     * Method final flag
     * @var boolean
     */
    protected $final = false;

    /**
     * Method body
     * @var string
     */
    protected $body = null;

    /**
     * Constructor
     *
     * Instantiate the method generator object
     *
     * @param  string  $name
     * @param  string  $visibility
     * @param  boolean $static
     */
    public function __construct($name, $visibility = 'public', $static = false)
    {
        $this->setName($name);
        $this->setVisibility($visibility);
        $this->setStatic($static);
    }

    /**
     * Set the method abstract flag
     *
     * @param  boolean $abstract
     * @return MethodGenerator
     */
    public function setAbstract($abstract = false)
    {
        $this->abstract = (boolean)$abstract;
        return $this;
    }

    /**
     * Get the method abstract flag
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set the method final flag
     *
     * @param  boolean $final
     * @return MethodGenerator
     */
    public function setFinal($final = false)
    {
        $this->final = (boolean)$final;
        return $this;
    }

    /**
     * Get the method final flag
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Add a method argument
     *
     * @param string  $name
     * @param mixed   $value
     * @param string  $type
     * @return MethodGenerator
     */
    public function addArgument($name, $value = null, $type = null)
    {
        $typeHintsNotAllowed = [
            'int',
            'integer',
            'boolean',
            'float',
            'string',
            'mixed'
        ];
        $argType = (!in_array($type, $typeHintsNotAllowed)) ? $type : null;
        $this->arguments[$name] = ['value' => $value, 'type' => $argType];

        if (null === $this->docblock) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }

        if (null !== $type) {
            if (substr($name, 0, 1) != '$') {
                $name = '$' . $name;
            }
            $this->docblock->setParam($type, $name);
        }

        return $this;
    }

    /**
     * Add method arguments
     *
     * @param array $args
     * @return MethodGenerator
     */
    public function addArguments(array $args)
    {
        foreach ($args as $arg) {
            $value = (isset($arg['value'])) ? $arg['value'] : null;
            $type  = (isset($arg['type'])) ? $arg['type'] : null;
            $this->addArgument($arg['name'], $value, $type);
        }
        return $this;
    }

    /**
     * Add a method argument (alias method for convenience)
     *
     * @param string  $name
     * @param mixed   $value
     * @param string  $type
     * @return MethodGenerator
     */
    public function addParameter($name, $value = null, $type = null)
    {
        $this->addArgument($name, $value, $type);
        return $this;
    }

    /**
     * Add method arguments (alias method for convenience)
     *
     * @param array $args
     * @return MethodGenerator
     */
    public function addParameters(array $args)
    {
        $this->addArguments($args);
        return $this;
    }

    /**
     * Get a method argument
     *
     * @param  string $name
     * @return array
     */
    public function getArgument($name)
    {
        return (isset($this->arguments[$name])) ? $this->arguments[$name] : null;
    }

    /**
     * Get the method arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get a method argument (alias method for convenience)
     *
     * @param  string $name
     * @return array
     */
    public function getParameter($name)
    {
        return (isset($this->arguments[$name])) ? $this->arguments[$name] : null;
    }

    /**
     * Get the method arguments (alias method for convenience)
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->arguments;
    }

    /**
     * Set the method body
     *
     * @param  string $body
     * @param  boolean $newline
     * @return MethodGenerator
     */
    public function setBody($body, $newline = true)
    {
        $this->body = $this->indent . '    ' .  str_replace(PHP_EOL, PHP_EOL . $this->indent . '    ', $body);
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Append to the method body
     *
     * @param  string  $body
     * @param  boolean $newline
     * @return MethodGenerator
     */
    public function appendToBody($body, $newline = true)
    {
        $body = str_replace(PHP_EOL, PHP_EOL . $this->indent . '    ', $body);
        $this->body .= $this->indent . '    ' . $body;
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Get the method body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }



    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $final    = ($this->final) ? 'final ' : null;
        $abstract = ($this->abstract) ? 'abstract ' : null;
        $static   = ($this->static) ? ' static' : null;
        $args     = $this->formatArguments();

        $this->output = PHP_EOL . ((null !== $this->docblock) ? $this->docblock->render() : null);
        $this->output .= $this->indent . $final . $abstract . $this->visibility .
            $static . ' function ' . $this->name . '(' . $args . ')';

        if (!empty($this->body)) {
            $this->output .= PHP_EOL . $this->indent . '{' . PHP_EOL;
            $this->output .= $this->body. PHP_EOL;
            $this->output .= $this->indent . '}';
        } else {
            $this->output .= ';';
        }

        $this->output .= PHP_EOL;

        return $this->output;
    }

    /**
     * Format the arguments
     *
     * @return string
     */
    protected function formatArguments()
    {
        $args = null;

        $i = 0;
        foreach ($this->arguments as $name => $arg) {
            $i++;
            $args .= (null !== $arg['type']) ? $arg['type'] . ' ' : null;
            $args .= (substr($name, 0, 1) != '$') ? "\$" . $name : $name;
            $args .= (null !== $arg['value']) ? " = " . $arg['value'] : null;
            if ($i < count($this->arguments)) {
                $args .= ', ';
            }
        }

        return $args;
    }

    /**
     * Print method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}