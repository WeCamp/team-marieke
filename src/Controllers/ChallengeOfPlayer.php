<?php declare(strict_types=1);

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Domain\Player;

final class ChallengeOfPlayer
{
    public function __invoke(Request $request)
    {
        /** @var Player $currentPlayer */
        $currentPlayer = $request->getAttribute('player');

        return new Response(Status::OK, [], json_encode([
            'challenger_username' => $currentPlayer->challengedBy(),
        ]));
    }
}
