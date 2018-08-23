<?php declare(strict_types=1);

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Authentication\AuthenticationContext;
use CorrectHorseBattery\Authentication\NoPlayerSignedOn;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\Repositories\Players;

final class ChallengePlayer
{
    private $authenticationContext;
    private $players;

    public function __construct(AuthenticationContext $authenticationContext, Players $players)
    {
        $this->authenticationContext = $authenticationContext;
        $this->players = $players;
    }

    public function __invoke(Request $request)
    {
        try {
            $challengingPlayer = $this->authenticationContext->currentSignedOnPlayer($request);
        } catch (NoPlayerSignedOn $e) {
            return new Response(Status::BAD_REQUEST, [], 'You must send a "Player" header with your username');
        } catch (PlayerDoesNotExist $e) {
            return new Response(Status::FORBIDDEN, [], 'You do not exist');
        }

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
