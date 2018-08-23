<?php

namespace CorrectHorseBattery;

use Amp\Http\Server\Response;
use Amp\Http\Status;

class Router
{
    private $routes = [
        '/' => \CorrectHorseBattery\Controllers\Login::class,
        '/players/challengeable' => \CorrectHorseBattery\Controllers\ChallengeablePlayers::class,
    ];

    public function route(string $url)
    {
        if (isset($this->routes[$url])) {
            return (new $this->routes[$url])();
        }

        return new Response(Status::NOT_FOUND);
    }
}
