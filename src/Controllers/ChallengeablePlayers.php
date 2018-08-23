<?php

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Authentication\AuthenticationContext;
use CorrectHorseBattery\Authentication\NoPlayerSignedOn;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\Repositories\Players;

class ChallengeablePlayers
{
    private $authenticationContext;

    public function __construct(AuthenticationContext $context)
    {
        $this->authenticationContext = $context;
    }

    public function __invoke(Request $request)
    {
        $repository = new Players;
        $allPlayers = $repository->getAll();

        try {
            $currentPlayer = $this->authenticationContext->currentSignedOnPlayer($request);
        } catch (NoPlayerSignedOn $e) {
            return new Response(Status::BAD_REQUEST, [], 'You must send a "Player" header with your username');
        } catch (PlayerDoesNotExist $e) {
            return new Response(Status::FORBIDDEN, [], 'You do not exist');
        }

        $challengeablePlayers = array_filter($allPlayers, function ($player) use ($currentPlayer) {
            return $player['username'] !== $currentPlayer->username();
        });

        return new Response(Status::OK, [
            'content-type' => 'application/json',
            "Access-Control-Allow-Origin" => '*',
        ], json_encode($challengeablePlayers));
    }
}
