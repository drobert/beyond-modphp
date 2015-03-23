<?php
// vim: set et ts=4 sts=4 sw=4 ai cindent:

// see: https://github.com/reactphp/zmq
require_once __DIR__.'/../../vendor/autoload.php';

// the config

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use BeyondModPhp\UserRepo;
use BeyondModPhp\UserRepoMockDelays;
use BeyondModPhp\UserRepoEventStream;
use BeyondModPhp\Monolog\ReactStreamHandler;

$loop = React\EventLoop\Factory::create();

$formatter = new LineFormatter("%message%\n");

$logger = new Logger('All');
$allEventsHandler = new ReactStreamHandler(new React\Stream\Stream(fopen(__DIR__.'/logs/app_logger_all.log', 'a'), $loop), Logger::DEBUG);
$allEventsHandler->setFormatter($formatter);
$shortEventsHandler = new ReactStreamHandler(new React\Stream\Stream(fopen(__DIR__.'/logs/app_logger_short.log', 'a'), $loop), Logger::ERROR, false);
$shortEventsHandler->setFormatter($formatter);
$errorHandler = new ReactStreamHandler(new React\Stream\Stream(fopen(__DIR__.'/logs/errors.log', 'a'), $loop), Logger::ERROR, false);

$logger->pushHandler($errorHandler);
$logger->pushHandler($shortEventsHandler);
$logger->pushHandler($allEventsHandler);

$errors = new Logger('Errors');
$errors->pushHandler($errorHandler);

// the app

$zmqContext = new React\ZMQ\Context($loop);

$zmqPull = $zmqContext->getSocket(\ZMQ::SOCKET_PULL);
$zmqPull->bind('tcp://127.0.0.1:5555');
$zmqPull->on('error', function($e) use ($errors) {
    $errors->error(print_r($e, true));
});
$zmqPull->on('message', function($msg) use ($logger) {
    $payload = json_decode($msg);
    if (Logger::ERROR == $payload->level) {
        $logger->error($payload->message);
    } else {
        $logger->info($payload->message);
    }
});

$loop->run();
