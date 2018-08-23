<?php declare(strict_types=1);

namespace CorrectHorseBattery\Authentication;

use Amp\Http\Server\Request;
use CorrectHorseBattery\Domain\Player;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
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
    public function currentSignedOnPlayer(Request $request): Player
    {
        $currentPlayer = $request->getHeader('Player');
        if (!$currentPlayer) {
            throw new NoPlayerSignedOn();
        }

        return $this->players->getByUsername($currentPlayer);
    }
}
