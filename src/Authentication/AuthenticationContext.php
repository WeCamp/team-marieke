<?php declare(strict_types=1);

namespace CorrectHorseBattery\Authentication;

use Amp\Http\Server\Request;
use CorrectHorseBattery\Repositories\Players;

final class AuthenticationContext
{
    private $players;

    public function __construct(Players $players)
    {
        $this->players = $players;
    }

    /**
     * @throws NoPlayerSignedOn
     * @throws PlayerDoesNotExist
     */
    public function currentSignedOnPlayer(Request $request): array
    {
        $allPlayers = $this->players->getAll();

        $currentPlayer = $request->getHeader('Player');
        if (!$currentPlayer) {
            throw new NoPlayerSignedOn();
        }

        $candidatePlayers = array_filter($allPlayers, function ($player) use ($currentPlayer) {
            return $player['username'] === $currentPlayer;
        });
        if (count($candidatePlayers) < 1) {
            throw new PlayerDoesNotExist();
        }

        return reset($candidatePlayers);
    }
}
