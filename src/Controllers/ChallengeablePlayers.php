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

        $currentPlayer = $request->getHeader('Player');
        if (!$currentPlayer) {
            return new Response(Status::BAD_REQUEST, [], 'You must send a "Player" header with your username');
        }

        $candidatePlayers = array_filter($allPlayers, function ($player) use ($currentPlayer) {
            return $player['username'] === $currentPlayer;
        });
        if (count($candidatePlayers) < 1) {
            return new Response(Status::FORBIDDEN, [], 'You do not exist');
        }

        $challengeablePlayers = array_filter($allPlayers, function ($player) use ($currentPlayer) {
            return $player['username'] !== $currentPlayer;
        });

        return new Response(Status::OK, [
            'content-type' => 'application/json',
            "Access-Control-Allow-Origin" => '*',
        ], json_encode($challengeablePlayers));
    }
}
