<?php declare(strict_types=1);

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Domain\Duel;
use CorrectHorseBattery\Domain\Duels;
use CorrectHorseBattery\Domain\Move;
use CorrectHorseBattery\Domain\Player;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\Repositories\Players;
use CorrectHorseBattery\Websockets\ContinuousCommunication;

final class MakeMove
{
    private $players;
    private $duels;
    private $continuousCommunication;

    public function __construct(Players $players, Duels $duels, ContinuousCommunication $continuousCommunication)
    {
        $this->players = $players;
        $this->duels = $duels;
        $this->continuousCommunication = $continuousCommunication;
    }

    public function __invoke(Request $request)
    {
        /** @var Player $movingPlayer */
        $movingPlayer = $request->getAttribute('player');

        $requestData = json_decode(yield $request->getBody()->read(), true);
        if (!is_array($requestData)
            || !array_key_exists('duel_id', $requestData)
            || !array_key_exists('move', $requestData)
        ) {
            return new Response(Status::BAD_REQUEST, [], 'No duel_id and/or move specified.');
        }

        $move = Move::fromString($requestData['move']);
        $duel = $this->duels->getById($requestData['duel_id']);

        try {
            $playerToAttack = $this->players->getByUsername($duel->otherPlayerThan($movingPlayer->username()));
        } catch (PlayerDoesNotExist $e) {
            throw new \LogicException($e->getMessage());
        }

        $duel->registerMove($movingPlayer->username(), $move);

        $this->duels->save($duel);

        if ($duel->hasFinished()) {
            $this->sendOutcomeOfDuel($movingPlayer->username(), $duel, $movingPlayer, $playerToAttack);
            $this->sendOutcomeOfDuel($playerToAttack->username(), $duel, $movingPlayer, $playerToAttack);
        }

        return new Response(Status::OK);
    }

    private function sendOutcomeOfDuel(
        string $username,
        Duel $duel,
        Player $attackingPlayer,
        Player $playerToAttack
    ): void {
        $this->continuousCommunication->sendDataToPlayer($username, json_encode([
            'is_draw' => $duel->isDraw(),
            'winner' => $duel->winner(),
            'attacks' => [
                $attackingPlayer->username() => $duel->moveOfPlayer($attackingPlayer->username()),
                $playerToAttack->username() => $duel->moveOfPlayer($playerToAttack->username()),
            ],
        ]));
    }
}
