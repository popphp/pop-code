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
 * Literal value class
 *
 * Wraps a raw PHP-source expression (e.g. 'self::FOO', 'Status::Active', 'new Foo()') that must be
 * emitted verbatim wherever a generator would otherwise quote/escape a plain PHP value.
 *
 * @category   Pop
 * @package    Pop\Code
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.5
 */
class Literal
{

    /**
     * The raw source expression
     * @var string
     */
    protected string $value;

    /**
     * Constructor
     *
     * @param  string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the raw source expression
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
