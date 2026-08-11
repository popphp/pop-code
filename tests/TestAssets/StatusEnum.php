<?php
/**
 * Fixture: a backed enum with an interface, a used trait, a real constant, a documented case, and methods.
 */
namespace Pop\Code\Test\TestAssets;

interface ColorfulInterface
{
    public function color(): string;
}

trait StatusHelperTrait
{
    public function isActive(): bool
    {
        return $this === self::Active;
    }
}

enum StatusEnum: string implements ColorfulInterface
{
    use StatusHelperTrait;

    /**
     * The active status
     */
    case Active = 'active';
    case Inactive = 'inactive';

    const DEFAULT = self::Active;

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Inactive => 'red',
        };
    }

    public static function fromLabel(string $label): self
    {
        return self::Active;
    }

}
