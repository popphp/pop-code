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
 * Function generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class FunctionGenerator extends AbstractGenerator
{

    use Traits\NameTrait, Traits\DocblockTrait, Traits\FunctionTrait, Traits\BodyTrait;

    /**
     * Function interface flag
     * @var boolean
     */
    protected $closure = false;

    /**
     * Function body
     * @var string
     */
    protected $body = null;

    /**
     * Constructor
     *
     * Instantiate the function generator object
     *
     * @param  string  $name
     * @param  boolean $closure
     */
    public function __construct($name = null, $closure = false)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        $this->setAsClosure($closure);
    }

    /**
     * Set the function closure flag
     *
     * @param  boolean $closure
     * @return FunctionGenerator
     */
    public function setAsClosure($closure = false)
    {
        $this->closure = (boolean)$closure;
        return $this;
    }

    /**
     * Get the function closure flag
     *
     * @return boolean
     */
    public function isClosure()
    {
        return $this->closure;
    }

    /**
     * Method to parse a function or closure
     *
     * @param  mixed  $func
     * @param  string $name
     * @throws \ReflectionException
     * @return FunctionGenerator
     */
    public static function parse($func, $name = null)
    {
        $reflection       = new \ReflectionFunction($func);
        $reflectionCode   = \ReflectionFunction::export($func, true);
        $reflectionName   = $reflection->getName();
        $reflectionParams = $reflection->getParameters();
        $isClosure        = ($reflectionName == '{closure}');

        if ((null === $name) && !($isClosure)) {
            $name = $reflectionName;
        }

        $function = new static($name, $isClosure);

        foreach ($reflectionParams as $key => $reflectionParam) {
            $paramName  = $reflectionParam->getName();
            $paramType  = $reflectionParam->getType();
            $paramType  = (!empty($paramType) && ($paramType instanceof \ReflectionType)) ? $paramType->getName() : null;

            try {
                $paramValue = $reflectionParam->getDefaultValue();
            } catch (\ReflectionException $e) {
                $paramValue = null;
            }

            $function->addArgument($paramName, $paramValue, $paramType);
        }

        // Parse the body if available
        if (strpos($reflectionCode, '@@') !== false) {
            $file = substr($reflectionCode, (strpos($reflectionCode, '@@') + 2));
            $file = (strpos($file, '.phtml') !== false) ?
                trim(substr($file, 0, (strpos($file, '.phtml') + 5))) : trim(substr($file, 0, (strpos($file, '.php') + 4)));

            if (file_exists($file)) {
                $lines     = file($file);
                $startLine = $reflection->getStartLine() - 1;
                $endLine   = $reflection->getEndLine() - 1;
                $length    = $endLine - $startLine;
                $body      = null;

                if (isset($lines[$startLine]) && isset($lines[$endLine])) {
                    $lines = array_slice($lines, ($startLine + 1), ($length - 1));
                    if (isset($lines[0]) && (substr($lines[0], 0, 1) == ' ')) {
                        $spaces = strlen($lines[0]) - strlen(ltrim($lines[0]));
                        if ($spaces > 0) {
                            $lines = array_map(function($value) use ($spaces) {
                                if (substr($value, 0, $spaces) == str_repeat(' ', $spaces)) {
                                    $value = substr($value, $spaces);
                                }
                                return $value;
                            }, $lines);
                        }
                    }
                    $body  = implode('', $lines);
                }

                if (!empty($body)) {
                    $function->setBody($body, false);
                }
            }
        }

        return $function;
    }

    /**
     * Render function
     *
     * @throws Exception
     * @return string
     */
    public function render()
    {
        if (null === $this->name) {
            throw new Exception('Error: The function name has not been set.');
        }

        $args = $this->formatArguments();

        $this->output = PHP_EOL . ((null !== $this->docblock) ? $this->docblock->render() : null);
        if ($this->closure) {
            $this->output .= $this->printIndent() . '$' . $this->name .' = function(' . $args . ')';
        } else {
            $this->output .= $this->printIndent() . 'function ' . $this->name . '(' . $args . ')';
        }

        $this->output .= PHP_EOL . $this->printIndent() . '{' . PHP_EOL;
        $this->output .= $this->body. PHP_EOL;
        $this->output .= $this->printIndent() . '}';

        if ($this->closure) {
            $this->output .= ';';
        }

        $this->output .= PHP_EOL;

        return $this->output;
    }

    /**
     * Print function
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}