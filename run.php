<?php
declare(ticks = 1);
use core\LiverRecorder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

error_reporting(E_ALL);
require './vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');
if(isset($argv[2]) && $argv[2] === 'daemon'){
    $pid = \core\Process::daemon();
}
$loggerConfig = require './logger.php';
$path = $loggerConfig['channels'][$loggerConfig['default']]['path'];
$logger =new Logger($path);
$stream = new StreamHandler($path);
$stream->setFormatter(new \Monolog\Formatter\LineFormatter(null,'Y-m-d H:i:s'));
$logger->pushHandler($stream);


if(!isset($argv[1])){
    $logger->debug('please input the room id');
    exit;
}
$roomId = $argv[1];


$recorder = new LiverRecorder();
$recorder->setLogger($logger);

while(true){

    $recorder->run($roomId);
    sleep(60);
}
