<?php declare(strict_types=1);

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\Repositories\Players;

final class ChallengePlayer
{
    private $players;

    public function __construct(Players $players)
    {
        $this->players = $players;
    }

    public function __invoke(Request $request)
    {
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

        return new Response(Status::OK);
    }
}
