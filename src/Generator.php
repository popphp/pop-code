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
namespace Pop\Code;

use Pop\Code\Generator\Traits;

/**
 * Generator code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class Generator extends Generator\AbstractGenerator
{

    use Traits\NamespaceTrait, Traits\DocblockTrait, Traits\BodyTrait;

    /**
     * Code generator object
     * @var Generator\InterfaceGenerator
     */
    protected $code = null;

    /**
     * Environment setting, i.e. #!/usr/bin/php
     * @var string
     */
    protected $env = null;

    /**
     * Flag to close the code file with ?>
     * @var boolean
     */
    protected $close = false;

    /**
     * Code filename
     * @var string
     */
    protected $filename = null;

    /**
     * Constructor
     *
     * Instantiate the code generator object
     *
     * @param  Generator\GeneratorInterface $code
     */
    public function __construct(Generator\GeneratorInterface $code = null)
    {
        if (null !== $code) {
            $this->setCode($code);
        }
    }

    /**
     * Set the code generator object
     *
     * @param  Generator\GeneratorInterface $code
     * @return Generator
     */
    public function setCode(Generator\GeneratorInterface $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Has code generator object
     *
     * @return boolean
     */
    public function hasCode()
    {
        return (null !== $this->code);
    }

    /**
     * Access the code generator object
     *
     * @return Generator\GeneratorInterface
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Access the code generator object (alias method)
     *
     * @return Generator\GeneratorInterface
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Set the code close tag flag
     *
     * @param  boolean $close
     * @return Generator
     */
    public function setCloseTag($close = false)
    {
        $this->close = (boolean)$close;
        return $this;
    }

    /**
     * Determine if the code close tag flag is set
     *
     * @return boolean
     */
    public function hasCloseTag()
    {
        return $this->close;
    }

    /**
     * Set the environment
     *
     * @param  string $env
     * @return Generator
     */
    public function setEnv($env = null)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * Get the environment
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Determine if the environment is set
     *
     * @return boolean
     */
    public function hasEnv()
    {
        return (null !== $this->env);
    }

    /**
     * Set the filename
     *
     * @param  string $filename
     * @return Generator
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Has filename
     *
     * @return boolean
     */
    public function hasFilename()
    {
        return (null !== $this->filename);
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Create config file
     *
     * @param  array   $config
     * @param  int     $indent
     * @param  boolean $newline
     * @return Generator
     */
    public function createConfig(array $config, $indent = 4, $newline = true)
    {
        $body = var_export($config, true);

        if ((null !== $indent) && (($indent % 2) == 0)) {
            $multiplier     = $indent / 2;
            $replacePattern = str_repeat('$1', $multiplier) . '$2';
        } else {
            $replacePattern = '$1$1$2';
        }

        $body    = preg_replace("/^([ ]*)(.*)/m", $replacePattern, $body);
        $bodyAry = preg_split("/\r\n|\n|\r/", $body);
        $bodyAry = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $bodyAry);
        $body    = implode(PHP_EOL, array_filter(["["] + $bodyAry));

        $this->setBody('return ' . $body . ';', $newline);

        return $this;
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $this->output = '';

        if (null !== $this->env) {
            $this->output .= $this->env . PHP_EOL;
        }

        $this->output .= '<?php' . PHP_EOL;
        $this->output .= (null !== $this->docblock) ? $this->docblock->render() . PHP_EOL : null;

        if (null !== $this->namespace) {
            $this->output .= $this->namespace->render() . PHP_EOL;
        }

        if (null !== $this->code) {
            $this->output .= $this->code->render() . PHP_EOL;
        }

        if (null !== $this->body) {
            $this->output .= PHP_EOL . $this->body . PHP_EOL . PHP_EOL;
        }

        if ($this->close) {
            $this->output .= '?>' . PHP_EOL;
        }

        return $this->output;
    }

    /**
     * Write to file
     *
     * @param  string $filename
     * @throws Exception
     * @return void
     */
    public function writeToFile($filename = null)
    {
        if ((null !== $this->filename) && (null === $filename)) {
            $filename = $this->filename;
        }
        if (empty($filename)) {
            throw new Exception('Error: The filename has not been set.');
        }

        file_put_contents($filename, $this->render());
    }

    /**
     * Output to HTTP
     *
     * @param  string  $filename
     * @param  boolean $forceDownload
     * @param  array   $headers
     * @return void
     */
    public function outputToHttp($filename = null, $forceDownload = false, array $headers = [])
    {
        if ((null !== $this->filename) && (null === $filename)) {
            $filename = $this->filename;
        }
        if (empty($filename)) {
            $filename = 'code.php';
        }

        $headers['Content-Type']        = 'text/plain';
        $headers['Content-Disposition'] = (($forceDownload) ? 'attachment; ' : null) . 'filename=' . $filename;

        // Send the headers and output the PDF
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK');
            foreach ($headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        echo $this->render();
    }

    /**
     * Print code
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}