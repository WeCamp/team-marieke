<?php

namespace CorrectHorseBattery\Controllers;

use Amp\Http\Server\Response;
use Amp\Http\Status;
use CorrectHorseBattery\Repositories\Players;

class Login
{
    public function __invoke()
    {
        $playersRepository = new Players;
        return new Response(Status::OK, [], json_encode($playersRepository->getAll()));
    }
}
