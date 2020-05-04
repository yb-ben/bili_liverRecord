<?php
declare(ticks = 1);
use core\LiverRecorder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

error_reporting(E_ALL);
require './vendor/autoload.php';


$pid = \core\Process::daemon();


if(!isset($argv[1])){
    die('please input the liver room id');
}
$roomId = $argv[1];

$loggerConfig = require './logger.php';
$path = $loggerConfig['channels'][$loggerConfig['default']]['path'];
$logger =new Logger($path);
$logger->pushHandler(new StreamHandler($path));


$recorder = new LiverRecorder();
$recorder->setLogger($logger);

while(true){

    $recorder->run($roomId);
    sleep(60);
}