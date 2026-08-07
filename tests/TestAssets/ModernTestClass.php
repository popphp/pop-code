<?php
/**
 * Modern-PHP-syntax fixture: typed properties/constants, scalar/const-reference defaults
 */
namespace Pop\Code\Test\TestAssets;

class ModernTestClass
{

    public const int LIMIT = 10;
    protected const string LABEL = 'modern';
    private const bool FLAG = true;
    const LEGACY = 'legacy-value';

    protected int $count = 0;
    protected string $label = 'hello';
    public ?string $noDefault;

    public function greet(string $name = 'world', bool $loud = false, ?string $suffix = null, string $fallback = self::LEGACY): string
    {
        return $loud ? strtoupper($name) : $name;
    }

    public function untypedFloatDefault($noType = 1.5)
    {
        return $noType;
    }

    public function sum(int ...$numbers): int
    {
        return array_sum($numbers);
    }

    public function increment(int &$counter): void
    {
        $counter++;
    }

    public function collectByRef(&...$items): void
    {
    }

    public function unionTyped(int|string $x, ?string $y = null): int|string
    {
        return $x;
    }

    public function intersectionTyped(\Countable&\Traversable $x): \Countable&\Traversable
    {
        return $x;
    }

}
