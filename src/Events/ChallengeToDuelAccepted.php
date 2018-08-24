<?php declare(strict_types=1);

namespace CorrectHorseBattery\Events;

final class ChallengeToDuelAccepted
{
    private $challengingPlayer;
    private $challengedPlayer;

    public function __construct(string $challengingPlayer, string $challengedPlayer)
    {
        $this->challengingPlayer = $challengingPlayer;
        $this->challengedPlayer = $challengedPlayer;
    }

    public function challengingPlayer(): string
    {
        return $this->challengingPlayer;
    }

    public function challengedPlayer(): string
    {
        return $this->challengedPlayer;
    }
}
