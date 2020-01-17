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
 * Body generator class
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
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
    public function createReturnConfig(array $config, $indent = 4)
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

        $this->setBody('return ' . $body . ';', 0);

        return $this;
    }

    /**
     * Render body
     *
     * @return string
     */
    public function render()
    {
        $this->output  = PHP_EOL . ((null !== $this->docblock) ? $this->docblock->render() : null);
        $this->output .= $this->body. PHP_EOL;

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