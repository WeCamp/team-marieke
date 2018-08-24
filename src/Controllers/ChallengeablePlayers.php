<?php

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Repositories\Players;

class ChallengeablePlayers
{
    public function __invoke(Request $request)
    {
        $repository = new Players;
        $allPlayers = $repository->getAll();

        $currentPlayer = $request->getAttribute('player');

        $challengeablePlayers = array_filter($allPlayers, function ($player) use ($currentPlayer) {
            return $player['username'] !== $currentPlayer->username();
        });

        return new Response(Status::OK, [], json_encode($challengeablePlayers));
    }
}
