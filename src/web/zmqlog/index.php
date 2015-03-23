<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent:
// web/index.php
require_once __DIR__.'/../../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use BeyondModPhp\UserRepo;
use BeyondModPhp\UserRepoMockDelays;
use BeyondModPhp\UserRepoEcho;
use BeyondModPhp\UserRepoEventStream;
use Websoftwares\Monolog\Handler\ZMQHandler;

$app = new Silex\Application();

$app['zmq'] = $app->share(function() {
    $context = new \ZMQContext();
    // 'PUB' is pub/sub for all listeners
    // 'PUSH' is winds up with a single listener
    $publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUSH);
    // cannot seem to get 'ipc://' to work with zmq in php; various SO posts seem to agree
    //$publisher->connect('ipc:///tmp/beyond-modphp.ipc');
    // Note: 'connect' is for pushers/publishers, 'bind' for pullers/subscribers
    $publisher->connect('tcp://127.0.0.1:5555');
    return $publisher;
});

$app['logger'] = $app->share(function() {
    $logger = new Logger('app.logger');
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::ERROR));
    return $logger;
});

$app['delays.user.min.usec'] = 50000;
$app['delays.user.max.usec'] = 100000;
$app['repo.user'] = $app->share(function($app) {
    $main = new UserRepoMockDelays($app['delays.user.min.usec'], $app['delays.user.max.usec'], $app['logger']);

    $handler = new ZMQHandler($app['zmq']);
    $logger = new Logger('Main');
    $logger->pushHandler($handler);
   
    return new UserRepoEventStream($main, $logger);
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($app['repo.user']->loadByName($name));
});

$app->run();

//$app['zmq']->disconnect('ipc:///tmp/beyond-modphp.ipc');
// unclear if this is necessary
$app['zmq']->disconnect('tcp://127.0.0.1:5555');
