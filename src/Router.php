<?php

namespace CorrectHorseBattery;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Authentication\AuthenticationContext;
use CorrectHorseBattery\Repositories\Players;

class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => function () {
                return new \CorrectHorseBattery\Controllers\Login();
            },
            '/playerstochallenge' => function () {
                return new \CorrectHorseBattery\Controllers\ChallengeablePlayers(new AuthenticationContext(new Players()));
            },
        ];
    }

    public function route(Request $request)
    {
        $url = $request->getUri()->getPath();

        // If a controller for this URL exists, create it and execute it with the request
        if (isset($this->routes[$url])) {
            return ($this->routes[$url])()($request);
        }

        return new Response(Status::NOT_FOUND);
    }
}
