<?php declare(strict_types=1);

namespace CorrectHorseBattery\Domain;

final class Player
{
    private $username;

    /** @var string|null */
    private $challengedPlayer;

    /** @var string|null */
    private $challengedBy;

    private function __construct(string $username)
    {
        $this->username = $username;
    }

    public static function deserialize(array $state): Player
    {
        $instance = new Player($state['username']);
        $instance->challengedPlayer = $state['challengedPlayer'] ?? null;
        $instance->challengedBy = $state['challengedBy'] ?? null;
        return $instance;
    }

    public function challenge(Player $playerToChallenge)
    {
        $this->challengedPlayer = $playerToChallenge->username();
        $playerToChallenge->challengedBy = $this->username;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function serialize(): array
    {
        return [
            'username' => $this->username,
            'challengedPlayer' => $this->challengedPlayer,
            'challengedBy' => $this->challengedBy,
        ];
    }

    public function challengedBy(): ?string
    {
        return $this->challengedBy;
    }
}
