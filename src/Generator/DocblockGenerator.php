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
namespace Pop\Code\Generator;

/**
 * Abstract generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class DocblockGenerator extends AbstractGenerator
{

    /**
     * Docblock description
     * @var ?string
     */
    protected ?string $desc = null;

    /**
     * Docblock tags
     * @var array
     */
    protected array $tags = ['param' => []];

    /**
     * Constructor
     *
     * Instantiate the docblock generator object
     *
     * @param ?string $desc
     * @param int     $indent
     */
    public function __construct(?string $desc = null, int $indent = 4)
    {
        $this->setDesc($desc);
        $this->setIndent($indent);
    }

    /**
     * Set the docblock description
     *
     * @param  ?string $desc
     * @return DocblockGenerator
     */
    public function setDesc(?string $desc = null): DocblockGenerator
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * Get the docblock description
     *
     * @return string|null
     */
    public function getDesc(): string|null
    {
        return $this->desc;
    }

    /**
     * Has a docblock description
     *
     * @return bool
     */
    public function hasDesc(): bool
    {
        return ($this->desc !== null);
    }

    /**
     * Add a basic tag
     *
     * @param  string  $name
     * @param  ?string $desc
     * @return DocblockGenerator
     */
    public function addTag(string $name, ?string $desc = null): DocblockGenerator
    {
        $this->tags[$name] = $desc;
        return $this;
    }

    /**
     * Add basic tags
     *
     * @param  array $tags
     * @return DocblockGenerator
     */
    public function addTags(array $tags): DocblockGenerator
    {
        foreach ($tags as $name => $desc) {
            $this->tags[$name] = $desc;
        }
        return $this;
    }

    /**
     * Get a tag
     *
     * @param  string $name
     * @return string|null
     */
    public function getTag(string $name): string|null
    {
        return $this->tags[$name] ?? null;
    }

    /**
     * Has a tag
     *
     * @param  string $name
     * @return bool
     */
    public function hasTag(string $name): bool
    {
        return (isset($this->tags[$name]));
    }

    /**
     * Add a param tag
     *
     * @param  ?string $type
     * @param  ?string $var
     * @param  ?string $desc
     * @return DocblockGenerator
     */
    public function addParam(?string $type = null, ?string $var = null, ?string $desc = null): DocblockGenerator
    {
        $this->tags['param'][] = ['type' => $type, 'var' => $var, 'desc' => $desc];
        return $this;
    }

    /**
     * Add a param tag
     *
     * @param  array $params
     * @return DocblockGenerator
     */
    public function addParams(array $params): DocblockGenerator
    {
        $params = (isset($params[0]) && is_array($params[0])) ? $params : [$params];
        foreach ($params as $param) {
            $this->tags['param'][] = $param;
        }
        return $this;
    }

    /**
     * Get a param
     *
     * @param  int $index
     * @return array|null
     */
    public function getParam(int $index): array|null
    {
        return (isset($this->tags['param']) && isset($this->tags['param'][$index])) ? $this->tags['param'][$index] : null;
    }

    /**
     * Has a param
     *
     * @param  int $index
     * @return bool
     */
    public function hasParam(int $index): bool
    {
        return (isset($this->tags['param']) && isset($this->tags['param'][$index]));
    }

    /**
     * Add a return tag
     *
     * @param  string  $type
     * @param  ?string $desc
     * @return DocblockGenerator
     */
    public function setReturn(string $type, ?string $desc = null): DocblockGenerator
    {
        $this->tags['return'] = ['type' => $type, 'desc' => $desc];
        return $this;
    }

    /**
     * Get the return
     *
     * @return array|null
     */
    public function getReturn(): array|null
    {
        return $this->tags['return'] ?? null;
    }

    /**
     * Has a return
     *
     * @return bool
     */
    public function hasReturn(): bool
    {
        return (isset($this->tags['return']));
    }

    /**
     * Add a throws tag
     *
     * @param  string  $type
     * @param  ?string $desc
     * @return DocblockGenerator
     */
    public function setThrows(string $type, ?string $desc = null): DocblockGenerator
    {
        $this->tags['throws'] = ['type' => $type, 'desc' => $desc];
        return $this;
    }

    /**
     * Get the throws
     *
     * @return array|null
     */
    public function getThrows(): array|null
    {
        return $this->tags['throws'] ?? null;
    }

    /**
     * Has a throws
     *
     * @return bool
     */
    public function hasThrows(): bool
    {
        return (isset($this->tags['throws']));
    }

    /**
     * Render docblock
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = $this->printIndent() . '/**' . PHP_EOL;

        if (!empty($this->desc)) {
            $desc    = trim($this->desc);
            $descAry = explode("\n", $desc);
            $i = 0;
            foreach ($descAry as $d) {
                $i++;
                $this->output .= $this->printIndent() . ' * ' . wordwrap($d, 70, PHP_EOL . $this->printIndent() . " * ") . PHP_EOL;
                if ($i < count($descAry)) {
                    $this->output .= $this->printIndent() . ' * ' . PHP_EOL;
                }
            }
        }

        $this->output .= $this->formatTags();
        $this->output .= $this->printIndent() . ' */' . PHP_EOL;

        return $this->output;
    }

    /**
     * Format the docblock tags
     *
     * @return string
     */
    protected function formatTags(): string
    {
        $tags      = null;
        $tagLength = $this->getTagLength();

        // Format basic tags
        foreach ($this->tags as $tag => $desc) {
            if (($tag != 'param') && ($tag != 'return') && ($tag != 'throws')) {
                $tags .= $this->printIndent() . ' * @' . $tag .
                    str_repeat(' ', $tagLength - strlen($tag) + 1) .
                    $desc . PHP_EOL;
            }
        }

        // Format param tags
        foreach ($this->tags['param'] as $param) {
            $paramLength = $this->getParamLength();
            $tags .= $this->printIndent() . ' * @param';

            if (!empty($param['type'])) {
                $tags .= str_repeat(' ', $tagLength - 4) . $param['type'] .
                    str_repeat(' ', $paramLength - strlen($param['type']) + 1);
            }
            if (!empty($param['var'])) {
                $tags .= ' ' . $param['var'];
            }
            $tags .= ($param['desc'] !== null) ? $param['desc'] . PHP_EOL : PHP_EOL;
        }

        // Format throw tag
        if (array_key_exists('throws', $this->tags)) {
            $throws = $this->tags['throws']['type'];
            if (!empty($this->tags['throws']['desc'])) {
                $throws .= ' ' . $this->tags['throws']['desc'];
            }
            $tags .= $this->printIndent() . ' * @throws' .
                str_repeat(' ', $tagLength - 5) .
                $throws . PHP_EOL;
        }

        // Format return tag
        if (array_key_exists('return', $this->tags)) {
            $tags .= $this->printIndent() . ' * @return' .
                str_repeat(' ', $tagLength - 5) .
                $this->tags['return']['type'];
            if ($this->tags['return']['desc'] !== null) {
                $tags .= ' ' . $this->tags['return']['desc'] . PHP_EOL;
            } else {
                $tags .= PHP_EOL;
            }
        }

        return (($tags !== null) && ($this->desc !== null)) ? $this->printIndent() . ' * ' . PHP_EOL . $tags : $tags;
    }

    /**
     * Get the longest tag length
     *
     * @return int
     */
    protected function getTagLength(): int
    {
        $length = 0;

        foreach ($this->tags as $key => $value) {
            if (strlen($key) > $length) {
                $length = strlen($key);
            }
        }

        return $length;
    }

    /**
     * Get the longest param type length
     *
     * @return int
     */
    protected function getParamLength(): int
    {
        $length = 0;

        foreach ($this->tags['param'] as $param) {
            if (!empty($param['type']) && (strlen($param['type']) > $length)) {
                $length = strlen($param['type']);
            }
        }

        return $length;
    }

    /**
     * Print docblock
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}