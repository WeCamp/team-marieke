<?php declare(strict_types=1);

namespace CorrectHorseBattery\Domain;

final class Duel
{
    private $id;
    private $moves = [];
    private $usernameOfFirstPlayer;
    private $usernameOfSecondPlayer;

    private function __construct(string $id, string $usernameOfFirstPlayer, string $usernameOfSecondPlayer)
    {
        $this->id = $id;
        $this->usernameOfFirstPlayer = $usernameOfFirstPlayer;
        $this->usernameOfSecondPlayer = $usernameOfSecondPlayer;
    }

    public static function begin(string $id, string $usernameOfFirstPlayer, string $usernameOfSecondPlayer): Duel
    {
        return new Duel($id, $usernameOfFirstPlayer, $usernameOfSecondPlayer);
    }

    public function registerMove(string $username, Move $move): void
    {
        $this->moves[$username] = $move;
    }

    public function hasFinished(): bool
    {
        return count($this->moves) === 2;
    }

    public function winner(): ?string
    {
        $result = "TWLLTWWLT"[$this->calculateWinner()];

        if($result == "W") {
            return $this->usernameOfSecondPlayer;
        }
        if($result == "L") {
            return $this->usernameOfFirstPlayer;
        }

        return null;
    }

    public function moveOfPlayer(string $username): Move
    {
        return $this->moves[$username];
    }

    private function calculateWinner()
    {
        $firstPlayer = (int)$this->moveOfPlayer($this->usernameOfFirstPlayer)->toString();
        $secondPlayer = (int)$this->moveOfPlayer($this->usernameOfSecondPlayer)->toString();
        return $firstPlayer * 3 + $secondPlayer;
    }

    public function otherPlayerThan(string $username): string
    {
        return $this->usernameOfFirstPlayer === $username
            ? $this->usernameOfSecondPlayer
            : $this->usernameOfFirstPlayer;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isDraw(): bool
    {
        /** @var Move[] $movesAsList */
        $movesAsList = array_values($this->moves);
        return $movesAsList[0]->equals($movesAsList[1]);
    }
}
