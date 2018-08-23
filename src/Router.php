<?php

namespace CorrectHorseBattery;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;

class Router
{
    private $routes = [
        '/' => \CorrectHorseBattery\Controllers\Login::class,
        '/playerstochallenge' => \CorrectHorseBattery\Controllers\ChallengeablePlayers::class,
    ];

    public function route(Request $request)
    {
        $url = $request->getUri()->getPath();

        // If a controller for this URL exists, create it and execute it with the request
        if (isset($this->routes[$url])) {
            return (new $this->routes[$url])($request);
        }

        return new Response(Status::NOT_FOUND);
    }
}
