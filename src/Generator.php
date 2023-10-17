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
namespace Pop\Code;

use Pop\Code\Generator\GeneratorInterface;
use Pop\Code\Generator\Traits;

/**
 * Generator code class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Generator extends Generator\AbstractGenerator
{

    use Traits\DocblockTrait;

    /**
     * Code generator objects
     * @var array
     */
    protected array $code = [];

    /**
     * Namespaces for the code generator objects
     * @var array
     */
    protected array $namespaces = [];

    /**
     * Environment setting, i.e. #!/usr/bin/php
     * @var ?string
     */
    protected ?string $env = null;

    /**
     * Flag to close the code file with ?>
     * @var bool
     */
    protected bool $close = false;

    /**
     * Code filename
     * @var ?string
     */
    protected ?string $filename = null;

    /**
     * Constructor
     *
     * Instantiate the code generator object
     *
     * @param  mixed $code
     * @throws Exception
     */
    public function __construct(mixed $code = null)
    {
        if ($code !== null) {
            if (is_array($code)) {
                $this->addCodeObjects($code);
            } else if ($code instanceof GeneratorInterface) {
                $this->addCodeObject($code);
            } else {
                throw new Exception('Error: The code parameter was not the correct type.');
            }
        }
    }

    /**
     * Add code generator objects
     *
     * @param  array $codeObjects
     * @return Generator
     */
    public function addCodeObjects(array $codeObjects): Generator
    {
        foreach ($codeObjects as $namespace => $codeObject) {
            if (!is_numeric($namespace)) {
                if (is_array($codeObject)) {
                    foreach ($codeObject as $code) {
                        $this->addCodeObject($code, $namespace);
                    }
                } else {
                    $this->addCodeObject($codeObject, $namespace);
                }
            } else {
                $this->addCodeObject($codeObject);
            }
        }
        return $this;
    }

    /**
     * Add a code generator object
     *
     * @param  Generator\GeneratorInterface $codeObject
     * @param  ?string                       $namespace
     * @return Generator
     */
    public function addCodeObject(Generator\GeneratorInterface $codeObject, ?string $namespace = null): Generator
    {
        $this->code[] = $codeObject;

        if ($namespace !== null) {
            $key = count($this->code) - 1;
            $this->namespaces[$key] = $namespace;
        }
        return $this;
    }

    /**
     * Has code generator objects
     *
     * @return bool
     */
    public function hasCode(): bool
    {
        return (!empty($this->code));
    }

    /**
     * Access the code generator object
     *
     * @return array
     */
    public function getCode(): array
    {
        return $this->code;
    }

    /**
     * Access the code generator objects (alias method)
     *
     * @return array
     */
    public function code(): array
    {
        return $this->code;
    }

    /**
     * Set the code close tag flag
     *
     * @param  bool $close
     * @return Generator
     */
    public function setCloseTag(bool $close = false): Generator
    {
        $this->close = $close;
        return $this;
    }

    /**
     * Determine if the code close tag flag is set
     *
     * @return bool
     */
    public function hasCloseTag(): bool
    {
        return $this->close;
    }

    /**
     * Set the environment
     *
     * @param  ?string $env
     * @return Generator
     */
    public function setEnv(?string $env = null): Generator
    {
        $this->env = $env;
        return $this;
    }

    /**
     * Get the environment
     *
     * @return string|null
     */
    public function getEnv(): string|null
    {
        return $this->env;
    }

    /**
     * Determine if the environment is set
     *
     * @return bool
     */
    public function hasEnv(): bool
    {
        return ($this->env !== null);
    }

    /**
     * Set the filename
     *
     * @param  string $filename
     * @return Generator
     */
    public function setFilename(string $filename): Generator
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Has filename
     *
     * @return bool
     */
    public function hasFilename(): bool
    {
        return ($this->filename !== null);
    }

    /**
     * Get filename
     *
     * @return string|null
     */
    public function getFilename(): string|null
    {
        return $this->filename;
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = '';

        if ($this->env !== null) {
            $this->output .= $this->env . PHP_EOL;
        }

        $this->output    .= '<?php' . PHP_EOL;
        $this->output    .= ($this->docblock !== null) ? $this->docblock->render() . PHP_EOL : null;
        $currentNamespace = null;
        $inNamespace      = false;

        foreach ($this->code as $key => $code) {
            if (isset($this->namespaces[$key]) && ($currentNamespace != $this->namespaces[$key])) {
                if ($currentNamespace !== null) {
                    $this->output .= '}' . PHP_EOL . PHP_EOL;
                }
                $namespace        = ($this->namespaces[$key] != '*') ? $this->namespaces[$key] . ' ' : null;
                $this->output    .= 'namespace ' . $namespace . '{' . PHP_EOL;
                $currentNamespace = $this->namespaces[$key];
                $inNamespace      = true;
            } else if (!isset($this->namespaces[$key]) && ($currentNamespace !== null)) {
                $this->output .= '}' . PHP_EOL . PHP_EOL;
                $inNamespace   = false;
            }

            if ($currentNamespace !== null) {
                $code->setIndent($code->getIndent() + $this->indent);
                if ($code->hasDocblock()) {
                    $code->getDocblock()->setIndent($code->getDocblock()->getIndent() + $this->indent);
                }
                if (in_array('Pop\Code\Generator\Traits\BodyTrait', class_uses($code))) {
                    $code->indentBody($this->indent);
                }
            }

            $this->output .= $code;
        }

        if ($inNamespace) {
            $this->output .= '}' . PHP_EOL . PHP_EOL;
        }

        if ($this->close) {
            $this->output .= '?>' . PHP_EOL;
        }

        return $this->output;
    }

    /**
     * Write to file
     *
     * @param  ?string $filename
     * @throws Exception
     * @return void
     */
    public function writeToFile(?string $filename = null): void
    {
        if (($this->filename !== null) && ($filename === null)) {
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
     * @param  ?string $filename
     * @param  bool    $forceDownload
     * @param  array   $headers
     * @return void
     */
    public function outputToHttp(?string $filename = null, bool $forceDownload = false, array $headers = []): void
    {
        if (($this->filename !== null) && ($filename === null)) {
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
    public function __toString(): string
    {
        return $this->render();
    }

}