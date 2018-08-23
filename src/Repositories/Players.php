<?php

namespace CorrectHorseBattery\Repositories;

class Players
{
    private $players = [
        ['username' => 'ingmar'],
        ['username' => 'jakob'],
        ['username' => 'gedi'],
    ];

    public function getAll()
    {
        return $this->players;
    }
}
