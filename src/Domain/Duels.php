<?php declare(strict_types=1);

namespace CorrectHorseBattery\Domain;

final class Duels
{
    private $elements = [];

    public function save(Duel $duel): void
    {
        $this->elements[$duel->id()] = $duel;
    }

    public function getById(string $duelId): Duel
    {
        return $this->elements[$duelId];
    }
}
