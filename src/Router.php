<?php

namespace CorrectHorseBattery;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Authentication\AuthenticationContext;
use CorrectHorseBattery\Authentication\NoPlayerSignedOn;
use CorrectHorseBattery\Domain\Duels;
use CorrectHorseBattery\Domain\PlayerDoesNotExist;
use CorrectHorseBattery\EventBus\EventBus;
use CorrectHorseBattery\Repositories\Players;
use CorrectHorseBattery\Websockets\ContinuousCommunication;

class Router
{
    private $routes;

    private $authenticationContextFactory;

    public function __construct(ContinuousCommunication $continuousCommunication, EventBus $eventBus)
    {
        $players = function (): Players {
            static $instance = null;
            if ($instance === null) {
                $instance = new Players();
            }
            return $instance;
        };
        $this->authenticationContextFactory = function () use ($players): AuthenticationContext {
            static $instance = null;
            if ($instance === null) {
                $instance = new AuthenticationContext($players());
            }
            return $instance;
        };
        $duels = function (): Duels {
            static $instance = null;
            if ($instance === null) {
                $instance = new Duels();
            }
            return $instance;
        };

        $this->routes = [
            '/' => function () {
                return new \CorrectHorseBattery\Controllers\Login();
            },
            '/playerstochallenge' => function () {
                return new \CorrectHorseBattery\Controllers\ChallengeablePlayers();
            },
            '/challengeplayer' => function () use ($continuousCommunication, $players) {
                return new \CorrectHorseBattery\Controllers\ChallengePlayer($players(), $continuousCommunication);
            },
            '/challengeofplayer' => function () {
                return new \CorrectHorseBattery\Controllers\ChallengeOfPlayer();
            },
            '/move' => function () use ($players, $duels, $continuousCommunication) {
                return new \CorrectHorseBattery\Controllers\MakeMove($players(), $duels(), $continuousCommunication);
            },
        ];

        $eventBus->subscribe(
            \CorrectHorseBattery\Events\ChallengeToDuelAccepted::class,
            new \CorrectHorseBattery\EventSubscribers\BeginDuelWhenChallengeToDuelAccepted($players(), $duels())
        );
    }

    public function route(Request $request)
    {
        $url = $request->getUri()->getPath();

        if ($url !== '/') {
            /** @var AuthenticationContext $authenticationContext */
            $authenticationContext = ($this->authenticationContextFactory)();
            try {
                $currentSignedOnPlayer = $authenticationContext->currentSignedOnPlayer($request);
            } catch (NoPlayerSignedOn $e) {
                return new Response(Status::BAD_REQUEST, [], 'You must send a "Player" header with your username');
            } catch (PlayerDoesNotExist $e) {
                return new Response(Status::FORBIDDEN, [], 'You do not exist');
            }

            $request->setAttribute('player', $currentSignedOnPlayer);
        }

        // If a controller for this URL exists, create it and execute it with the request
        if (isset($this->routes[$url])) {
            return $this->routes[$url]()($request);
        }

        return new Response(Status::NOT_FOUND);
    }
}
