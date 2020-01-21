<?php

require_once 'init.php';

use Main\ZKillConsumer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\Factory;

$log = new Logger('ZWSP');
$log->pushHandler(new StreamHandler('logs/ZWSP.log', Logger::INFO));
$log->info('Logger initialized');

$actions = include 'SubscriberActions.php';

$timestart = microtime(true);

$loop = Factory::create();
$connector = new Ratchet\Client\Connector($loop);

$app = (function (Ratchet\Client\WebSocket $conn) use ($connector, $loop, $actions, &$app, $log) {
    foreach ($actions as $action) {
        pecho('new subscriber sent');
        $log->info('New subscriber sent: '.$action);
        $conn->send($action);
    }

    $conn->on('message', function (MessageInterface $msg) use ($conn) {
        pecho("Received: $msg");
        (new ZKillConsumer())->consume($msg);
    });

    $conn->on('close', function ($code = null, $reason = null) use ($connector, $loop, $app, $log) {
        pecho("connection closed ({$code} - {$reason})");
        pecho('Reconnecting in 3 seconds');
        $loop->addTimer(3, function () use ($connector, $loop, $app, $log) {
            connectToWS($connector, $loop, $app, $log);
        });
    });
});

$loop->addSignal(SIGINT, $f = function (int $signal) use ($loop, &$f, $timestart, $log) {
    echo 'Caught user interrupt signal'.PHP_EOL;
    pecho('Work time: '.(microtime(true) - $timestart).'seconds');
    echo 'Signal: ', (string) $signal, PHP_EOL;
    $log->notice('Work interrupted by user. Work time '.(microtime(true) - $timestart).' seconds');
    $loop->stop();
    $loop->removeSignal(SIGINT, $f);
});

connectToWS($connector, $loop, $app, $log);

$loop->run();

function connectToWS($connector, $loop, $app, $log)
{
    $log->info('connecting to WS');
    $connector('wss://zkillboard.com:2096', [], ['Origin' => 'https://google.com'])
        ->then($app, function (Exception $e) use ($loop, $log) {
            $log->error("Could not connect: {$e->getMessage()}");
            pecho("Could not connect: {$e->getMessage()}");
            $loop->stop();
        });
}
