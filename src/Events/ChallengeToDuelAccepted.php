<?php declare(strict_types=1);

namespace CorrectHorseBattery\Events;

final class ChallengeToDuelAccepted
{
    private $challengingPlayer;
    private $challengedPlayer;
    private $duelId;

    public function __construct(string $duelId, string $challengingPlayer, string $challengedPlayer)
    {
        $this->challengingPlayer = $challengingPlayer;
        $this->challengedPlayer = $challengedPlayer;
        $this->duelId = $duelId;
    }

    public function challengingPlayer(): string
    {
        return $this->challengingPlayer;
    }

    public function challengedPlayer(): string
    {
        return $this->challengedPlayer;
    }

    public function duelId(): string
    {
        return $this->duelId;
    }
}
