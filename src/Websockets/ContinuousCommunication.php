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
    private $usernamesOfClients = [];

    public function onStart(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
        $this->endpoint->broadcast("broadcast alert!!");
    }

    public function onHandshake(Request $request, Response $response)
    {
        return $response;
    }

    public function onOpen(int $clientId, Request $request)
    {
        $this->endpoint->broadcast("onOpen test");
    }

    public function onData(int $clientId, Message $message)
    {
        $contents = json_decode(yield $message->read(), true);

        if(is_array($contents) && array_key_exists('type', $contents)) {
            switch ($contents['type']) {
                case "signOn":
                    $this->usernamesOfClients[$clientId] = $contents['username'];
                    break;
                case "challenged":
                    $username = $this->usernameFromClientId($clientId);
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

    private function usernameFromClientId(int $clientId)
    {
        return $this->usernamesOfClients[$clientId];
    }
}
