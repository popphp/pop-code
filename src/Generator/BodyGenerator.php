<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator;

/**
 * Body generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class BodyGenerator extends AbstractGenerator
{

    use Traits\DocblockTrait, Traits\BodyTrait;

    /**
     * Create return config
     *
     * @param  array $config
     * @param  int   $indent
     * @return BodyGenerator
     */
    public function createReturnConfig(array $config, int $indent = 4): BodyGenerator
    {
        $body = var_export($config, true);

        if (($indent !== null) && (($indent % 2) == 0)) {
            $multiplier     = $indent / 2;
            $replacePattern = str_repeat('$1', $multiplier) . '$2';
        } else {
            $replacePattern = '$1$1$2';
        }

        $body    = preg_replace("/^([ ]*)(.*)/m", $replacePattern, $body);
        $bodyAry = preg_split("/\r\n|\n|\r/", $body);
        $bodyAry = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $bodyAry);
        $body    = str_replace('NULL', 'null', implode(PHP_EOL, array_filter(["["] + $bodyAry)));

        $this->setBody('return ' . $body . ';', 0);

        return $this;
    }

    /**
     * Render body
     *
     * @return string
     */
    public function render(): string
    {
        $this->output  = PHP_EOL . (($this->docblock !== null) ? $this->docblock->render() : null);
        $this->output .= $this->body. PHP_EOL;

        return $this->output;
    }

    /**
     * Print function
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
