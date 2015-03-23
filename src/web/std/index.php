<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent:
// web/index.php
require_once __DIR__.'/../../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;
use BeyondModPhp\UserRepo;
use BeyondModPhp\UserRepoMockDelays;
use BeyondModPhp\UserRepoEventStream;

$app = new Silex\Application();
$app['logger'] = $app->share(function() {
    $logger = new Logger('Main');
    $logger->pushHandler(new ErrorLogHandler());
    return $logger;
});

$app['delays.user.min.usec'] = 50000;
$app['delays.user.max.usec'] = 100000;
$app['repo.user'] = $app->share(function($app) {
    $main = new UserRepoMockDelays($app['delays.user.min.usec'], $app['delays.user.max.usec'], $app['logger']);
    
    $eventLogger = new Logger('User.Event');
    $eventHandler = new ErrorLogHandler();
    $eventHandler->setFormatter(new LineFormatter("%datetime%\t%channel%\t%message%", "c"));
    $eventLogger->pushHandler($eventHandler);

    return new UserRepoEventStream($main, $eventLogger);
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($app['repo.user']->loadByName($name));
});

$app->run();
