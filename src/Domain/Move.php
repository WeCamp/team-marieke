<?php declare(strict_types=1);

namespace CorrectHorseBattery\Domain;

final class Move
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromString(int $value): Move
    {
        return new Move($value);
    }

    public function equals(Move $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString()
    {
        return $this->value;
    }
}
