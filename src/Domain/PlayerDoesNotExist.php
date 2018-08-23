<?php declare(strict_types=1);

namespace CorrectHorseBattery\Domain;

final class PlayerDoesNotExist extends \Exception
{
    public static function withUsername(string $username): PlayerDoesNotExist
    {
        return new PlayerDoesNotExist("User with username '$username' does not exist.");
    }
}
