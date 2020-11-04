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

$roomId = $argv[1];

Process::daemon(true,false);



$channelConfig = $logConfig['channels'][$logConfig['default']];
$path = ROOT_PATH. $channelConfig['path'].$roomId;
$logger =new Logger($logConfig['default']);
$stream = new StreamHandler($path);
$stream->setFormatter(new \Monolog\Formatter\LineFormatter(null,'Y-m-d H:i:s'));
$logger->pushHandler($stream);
$recorder = new LiverRecorder();
$recorder->setLogger($logger);

$pid = null;

Process::signal(15,function($signo)use($logger,&$pid){
    $logger->info('[SIG]'.$signo);
    Process::kill($pid);
    exit;
});

while(true){
    $process = new Process(function ()use($recorder,$roomId,$logger) {
        $logger->info('My pid:'.getmypid());
        while(true){
            $recorder->run($roomId);
            $logger->debug('finish a circle');
            sleep(60);
        }
    });

    $pid  = $process->start();
    $status = Process::wait(true);
    $logger->info("Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL);
}


