<?php

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Response;
use Amp\Http\Status;

class Login
{
    public function __invoke()
    {
        return new Response(Status::OK, [
            'content-type' => 'application/json',
            "Access-Control-Allow-Origin" => '*',
        ], json_encode([
            ['username' => 'ingmar'],
            ['username' => 'jakob'],
            ['username' => 'gedi'],
        ]));
    }
}
