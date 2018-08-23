<?php

namespace CorrectHorseBattery\Repositories;

use CorrectHorseBattery\Domain\Player;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;

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

    /**
     * @throws PlayerDoesNotExist
     */
    public function getByUsername(string $username): Player
    {
        $matchedUsers = array_filter($this->players, function ($player) use ($username) {
            return $player['username'] === $username;
        });

        if (count($matchedUsers) < 1) {
            throw PlayerDoesNotExist::withUsername($username);
        }

        return Player::deserialize(reset($matchedUsers));
    }

    public function save(Player $player): void
    {
        $this->players = array_filter($this->players, function ($playerInStorage) use ($player) {
            return $playerInStorage['username'] !== $player->username();
        });
        $this->players[] = $player->serialize();
    }
}
