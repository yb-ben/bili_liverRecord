<?php
use core\LiverRecorder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Swoole\Process;

require './vendor/autoload.php';
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
$logConfig = require 'config/log.php';
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

isset($argv[1]) || die('please input the room id');
isset($argv[2]) && $argv[2] === '--daemon' && Process::daemon(true);
$roomId = $argv[1];




$channelConfig = $logConfig['channels'][$logConfig['default']];
$path = ROOT_PATH. $channelConfig['path'].$roomId.'.log';
$logger =new Logger($logConfig['default']);
$stream = new StreamHandler($path);
$stream->setFormatter(new \Monolog\Formatter\LineFormatter(null,'Y-m-d H:i:s'));
$logger->pushHandler($stream);
$recorder = new LiverRecorder();
$recorder->setLogger($logger);

$pid = getmypid();
$logger->info('My pid:'.$pid);

$pidPath = ROOT_PATH . 'pid';
if (!file_exists($pidPath)) {
    mkdir($pidPath);
}
file_put_contents($pidPath.DIRECTORY_SEPARATOR.$roomId , $pid);

while(true){
    $recorder->run($roomId);
    $logger->debug('finish a circle');
    sleep(60);
}

