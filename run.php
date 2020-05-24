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


$recorder = new LiverRecorder();
$recorder->setLogger($logger);

if(isset($argv[1]) && $argv[1] === 'test'){

    $recorder->test('https://js.live-play.acgvideo.com/live-js/781742/live_389862071_60782122_1500.flv?wsSecret=b3c72a17e2c96130fd1bdeb73c164073&wsTime=1588569184&trid=d9673e0f839e424a9b30978bdbcbb7b7&pt=web&oi=2028384599&order=0&sig=no');
    exit;
}

if(!isset($argv[1])){
    $logger->debug('please input the room id');
    exit;
}
$roomId = $argv[1];

//while(true){
//
//    $recorder->run($roomId);
//    sleep(60);
//}
