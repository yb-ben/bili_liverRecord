<?php
use core\LiverRecorder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

error_reporting(E_ALL);
require './vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');

$loggerConfig = require './logger.php';
$path = $loggerConfig['channels'][$loggerConfig['default']]['path'];
$logger =new Logger($path);
$stream = new StreamHandler($path);
$stream->setFormatter(new \Monolog\Formatter\LineFormatter(null,'Y-m-d H:i:s'));
$logger->pushHandler($stream);


$recorder = new LiverRecorder();
$recorder->setLogger($logger);


if(!isset($argv[1])){
    $logger->debug('please input the room id');
    exit;
}
$roomId = $argv[1];

while(true){

    $recorder->run($roomId);
    $logger->debug('finish a circle');
    sleep(60);
}
