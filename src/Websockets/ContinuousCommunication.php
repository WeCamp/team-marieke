<?php

namespace CorrectHorseBattery\Websockets;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;

class ContinuousCommunication implements Application
{
    /** @var Endpoint */
    private $endpoint;
    private $clientIds = [];

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
    }

    public function onData(int $clientId, Message $message)
    {
        $contents = json_decode(yield $message->read(), true);

        if(is_array($contents) && array_key_exists('type', $contents)) {
            switch ($contents['type']) {
                case "signOn":
                    $this->clientIds[$contents['username']] = $clientId;
                    break;
                case "challenged":
                    break;
                case "acceptedChallenge":
                    break;
            }
        }
    }

    public function onClose(int $clientId, int $code, string $reason)
    {
        // do nothing
    }

    public function onStop()
    {
        // do nothing
    }

    public function sendDataToPlayer(string $username, string $data): void
    {
        if (array_key_exists($username, $this->clientIds)) {
            $this->endpoint->send($data, $this->clientIds[$username]);
        }
    }
}
