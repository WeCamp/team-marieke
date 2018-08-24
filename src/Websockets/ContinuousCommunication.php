<?php

namespace CorrectHorseBattery\Websockets;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;
use CorrectHorseBattery\EventBus\EventBus;
use CorrectHorseBattery\Events\ChallengeToDuelAccepted;

class ContinuousCommunication implements Application
{
    /** @var Endpoint */
    private $endpoint;
    private $clientIds = [];
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

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

        echo "Received websocket message: " . var_export($contents, true);

        if (is_array($contents) && array_key_exists('type', $contents)) {
            switch ($contents['type']) {
                case "signOn":
                    $this->clientIds[$contents['username']] = $clientId;
                    break;
                case 'challenge_response':
                    $challengingPlayer = $contents['challengingPlayer'];

                    if ($contents['accept']) {
                        $duelId = uniqid('', true);
                        $this->eventBus->dispatch(new ChallengeToDuelAccepted(
                            $duelId,
                            $contents['challengingPlayer'],
                            $contents['challengedPlayer']
                        ));
                    }

                    $this->sendDataToPlayer($challengingPlayer, json_encode([
                        'type' => 'challenge_response',
                        'challengingPlayer' => $contents['challengingPlayer'],
                        'challengedPlayer' => $contents['challengedPlayer'],
                        'accept' => $contents['accept'],
                        'duel_id' => $duelId ?? null,
                    ]));
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
