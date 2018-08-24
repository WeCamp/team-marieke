<?php

use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Server;
use Amp\Http\Server\Websocket\Websocket;
use Amp\Http\Status;
use Amp\Socket;
use CorrectHorseBattery\Websockets\ContinuousCommunication;

require_once __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $log = new \Monolog\Logger('app', [new \Monolog\Handler\StreamHandler('php://stderr')]);

    // Setup the websocket
    $continuousCommunication = new ContinuousCommunication;

    $websocket = new Websocket($continuousCommunication);

    $websocketServer = new Server([
        Socket\listen('0.0.0.0:9001'),
        Socket\listen('[::]:9001'),
    ], $websocket, $log);
    yield $websocketServer->start();

    // Set the HTTP servers
    $sockets = [
        Socket\listen("0.0.0.0:8080"),
        Socket\listen("[::]:8080"),
    ];

    $router = new \CorrectHorseBattery\Router($continuousCommunication);
    $server = new Server($sockets, new CallableRequestHandler(function (Request $request) use ($log, $router) {
        if ($request->getMethod() === 'OPTIONS') {
            return new Response(Status::OK, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Headers' => '*',
            ]);
        }
        return $router->route($request);
    }), $log);

    yield $server->start();

    // Stop the server gracefully when SIGINT is received.
    // This is technically optional, but it is best to call Server::stop().
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});
