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

use Pop\Code\Generator\DocblockGenerator;

/**
 * Name trait
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
trait FunctionTrait
{

    /**
     * Arguments
     * @var array
     */
    protected $arguments = [];

    /**
     * Function body
     * @var string
     */
    protected $body = null;

    /**
     * Add a method argument
     *
     * @param string  $name
     * @param mixed   $value
     * @param string  $type
     * @return FunctionTrait
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

        if (substr($name, 0, 1) != '$') {
            $name = '$' . $name;
        }
        $this->docblock->setParam($type, $name);

        return $this;
    }

    /**
     * Add method arguments
     *
     * @param array $args
     * @return FunctionTrait
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
     * Has a method argument
     *
     * @param  string $name
     * @return boolean
     */
    public function hasArgument($name)
    {
        return (isset($this->arguments[$name]));
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
     * Add a method argument (alias method for convenience)
     *
     * @param string  $name
     * @param mixed   $value
     * @param string  $type
     * @return FunctionTrait
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
     * @return FunctionTrait
     */
    public function addParameters(array $args)
    {
        $this->addArguments($args);
        return $this;
    }

    /**
     * Has a method argument (alias method for convenience)
     *
     * @param  string $name
     * @return boolean
     */
    public function hasParameter($name)
    {
        return $this->hasArgument($name);
    }

    /**
     * Get a method argument (alias method for convenience)
     *
     * @param  string $name
     * @return array
     */
    public function getParameter($name)
    {
        return $this->getArgument($name);
    }

    /**
     * Get the method arguments (alias method for convenience)
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->getArguments();
    }

    /**
     * Set the function body
     *
     * @param  string $body
     * @param  boolean $newline
     * @return FunctionTrait
     */
    public function setBody($body, $newline = true)
    {
        $this->body = $this->printIndent() . '    ' .  str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Append to the function body
     *
     * @param  string  $body
     * @param  boolean $newline
     * @return FunctionTrait
     */
    public function appendToBody($body, $newline = true)
    {
        $body = str_replace(PHP_EOL, PHP_EOL . $this->printIndent() . '    ', $body);
        $this->body .= $this->printIndent() . '    ' . $body;
        if ($newline) {
            $this->body .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Get the function body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Has method body
     *
     * @return boolean
     */
    public function hasBody()
    {
        return (null !== $this->body);
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

}