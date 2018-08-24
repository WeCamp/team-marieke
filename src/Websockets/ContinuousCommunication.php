<?php

namespace CorrectHorseBattery\Websockets;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;
use Amp\Loop;
use CorrectHorseBattery\Domain\Player;

class ContinuousCommunication implements Application
{
    /** @var Endpoint */
    private $endpoint;

    public function onStart(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function onHandshake(Request $request, Response $response)
    {
        return $response;
    }

    public function onOpen(int $clientId, Request $request)
    {
        // do nothing
    }

    public function onData(int $clientId, Message $message)
    {
        // do nothing
    }

    public function onClose(int $clientId, int $code, string $reason)
    {
        // do nothing
    }

    public function onStop()
    {
        Loop::cancel($this->watcher);
    }
}
