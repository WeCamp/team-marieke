<?php

use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Server;
use Amp\Http\Status;
use Amp\Socket;

require_once __DIR__ . '/../vendor/autoload.php';

Amp\Loop::run(function () {
    $sockets = [
        Socket\listen("0.0.0.0:8080"),
        Socket\listen("[::]:8080"),
    ];

    $log = new \Monolog\Logger('app', [new \Monolog\Handler\StreamHandler('php://stderr')]);

    $router = new \CorrectHorseBattery\Router;
    $server = new Server($sockets, new CallableRequestHandler(function (Request $request) use ($log, $router) {
        return $router->route($request->getUri()->getPath());
    }), $log);

    yield $server->start();

    // Stop the server gracefully when SIGINT is received.
    // This is technically optional, but it is best to call Server::stop().
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});
