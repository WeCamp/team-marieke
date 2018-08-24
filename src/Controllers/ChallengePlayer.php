<?php declare(strict_types=1);

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Domain\Player;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\Repositories\Players;
use CorrectHorseBattery\Websockets\ContinuousCommunication;

final class ChallengePlayer
{
    private $players;
    private $continuousCommunication;

    public function __construct(Players $players, ContinuousCommunication $continuousCommunication)
    {
        $this->players = $players;
        $this->continuousCommunication = $continuousCommunication;
    }

    public function __invoke(Request $request)
    {
        /** @var Player $challengingPlayer */
        $challengingPlayer = $request->getAttribute('player');

        $requestData = json_decode(yield $request->getBody()->read(), true);
        if (!is_array($requestData) || !array_key_exists('user_to_challenge', $requestData)) {
            return new Response(Status::BAD_REQUEST, [], 'user_to_challenge not provided.');
        }

        try {
            $playerToChallenge = $this->players->getByUsername($requestData['user_to_challenge']);
        } catch (PlayerDoesNotExist $e) {
            return new Response(Status::NOT_FOUND, [], $e->getMessage());
        }

        $challengingPlayer->challenge($playerToChallenge);

        $this->players->save($challengingPlayer);
        $this->players->save($playerToChallenge);

        $this->continuousCommunication->sendDataToPlayer($playerToChallenge->username(), json_encode([
            'type' => 'challenge_to_duel',
            'challenging_player' => $challengingPlayer->username(),
        ]));

        return new Response(Status::OK);
    }
}
