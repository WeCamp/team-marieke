<?php

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorstBattery\Repositories\Players;

class Login
{
    public function __invoke()
    {
        $playersRepository = new Players;
        $players = json_encode($playersRepository->getAll());

        return new Response(Status::OK, [
            'content-type' => 'application/json',
            "Access-Control-Allow-Origin" => '*',
        ], $players);
    }
}
